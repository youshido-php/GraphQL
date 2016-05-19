<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 1:05 AM
*/

namespace Youshido\GraphQL;

use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Field\SchemaField;
use Youshido\GraphQL\Introspection\Field\TypeDefinitionField;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentInterface;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\AbstractInterfaceTypeInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;
use Youshido\GraphQL\Validator\Exception\ResolveException;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidatorInterface;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;

class Processor
{
    use ErrorContainerTrait;

    const TYPE_NAME_QUERY = '__typename';

    /** @var  array */
    protected $data;

    /** @var ResolveValidatorInterface */
    protected $resolveValidator;

    /** @var SchemaValidator */
    protected $schemaValidator;

    /** @var AbstractSchema */
    protected $schema;

    /** @var Request */
    protected $request;

    public function __construct()
    {
        $this->resolveValidator = new ResolveValidator();
        $this->schemaValidator  = new SchemaValidator();
    }

    public function setSchema(AbstractSchema $schema)
    {
        if (!$this->schemaValidator->validate($schema)) {
            $this->mergeErrors($this->schemaValidator);

            return;
        }

        $this->schema = $schema;

        $schemaField = new SchemaField();
        $schemaField->setSchema($schema);

        $this->schema->addQueryField($schemaField);
        $this->schema->addQueryField(new TypeDefinitionField());
    }

    public function processRequest($payload, $variables = [])
    {
        if (!$this->getSchema()) {
            $this->addError(new ConfigurationException('You have to set GraphQL Schema to process'));

            return $this;
        }
        if (empty($payload)) return $this;

        $this->data = [];

        try {
            $this->parseAndCreateRequest($payload, $variables);

            foreach ($this->request->getQueries() as $query) {
                if ($queryResult = $this->executeQuery($query, $this->getSchema()->getQueryType())) {
                    $this->data = array_merge($this->data, $queryResult);
                };
            }

            foreach ($this->request->getMutations() as $mutation) {
                if ($mutationResult = $this->executeMutation($mutation, $this->getSchema()->getMutationType())) {
                    $this->data = array_merge($this->data, $mutationResult);
                }
            }

        } catch (\Exception $e) {
            $this->resolveValidator->clearErrors();

            $this->resolveValidator->addError($e);
        }

        return $this;
    }

    protected function parseAndCreateRequest($query, $variables = [])
    {
        $parser = new Parser();

        $data = $parser->parse($query);

        $this->request = new Request($data);
        $this->request->setVariables($variables);
    }

    /**
     * @param Query|Field        $query
     * @param AbstractObjectType $currentLevelSchema
     * @param null               $contextValue
     * @return array|bool|mixed
     */
    protected function executeQuery($query, $currentLevelSchema, $contextValue = null)
    {
        $currentLevelSchema = $currentLevelSchema->getNullableType(); //todo: somehow we need to validate for not null

        if (!$this->resolveValidator->objectHasField($currentLevelSchema, $query)) {
            return null;
        }

        /** @var AbstractField $field */
        $field = $currentLevelSchema->getField($query->getName());
        $alias = $query->getAlias() ?: $query->getName();

        if ($query instanceof AstField) {
            $value = $this->processAstFieldQuery($query, $contextValue, $field);
        } else {
            if (!$this->resolveValidator->validateArguments($field, $query, $this->request)) {
                return null;
            }

            $value = $this->processFieldTypeQuery($query, $contextValue, $field);

        }

        return [$alias => $value];
    }

    /**
     * @param Mutation   $mutation
     * @param ObjectType $currentLevelSchema
     * @return array|null
     * @throws ConfigurationException
     */
    protected function executeMutation(Mutation $mutation, $currentLevelSchema)
    {
        if (!$this->resolveValidator->objectHasField($currentLevelSchema, $mutation)) {
            return null;
        }

        /** @var AbstractField $field */
        $field     = $currentLevelSchema->getConfig()->getField($mutation->getName());
        $alias     = $mutation->getAlias() ?: $mutation->getName();
        $fieldType = $field->getType();

        if (!$this->resolveValidator->validateArguments($field, $mutation, $this->request)) {
            return null;
        }

        $resolvedValue = $this->resolveFieldValue($field, null, $mutation);

        if (!$this->resolveValidator->validateResolvedValueType($resolvedValue, $fieldType)) {
            return [$alias => null];
        }

        $value = $resolvedValue;
        if ($mutation->hasFields()) {
            if (TypeService::isAbstractType($fieldType)) {
                /** @var AbstractInterfaceTypeInterface $fieldType */
                $outputType = $fieldType->resolveType($resolvedValue);
            } else {
                /** @var AbstractType $outputType */
                $outputType = $fieldType;
            }

            $value = $this->collectTypeResolvedValue($outputType, $resolvedValue, $mutation);
        }

        return [$alias => $value];
    }

    /**
     * @param AstField      $astField
     * @param mixed         $contextValue
     * @param AbstractField $field
     *
     * @return array|mixed|null
     */
    protected function processAstFieldQuery(AstField $astField, $contextValue, AbstractField $field)
    {
        $value            = null;
        $fieldType        = $field->getType();
        $preResolvedValue = $this->getPreResolvedValue($contextValue, $astField, $field);

        if ($preResolvedValue && !$this->resolveValidator->validateResolvedValueType($preResolvedValue, $field->getType())) {
            return null;
        }

        if ($fieldType->getKind() == TypeMap::KIND_LIST) {
            $listValue = [];
            foreach ($preResolvedValue as $resolvedValueItem) {
                $type = $fieldType->getNamedType();

                if (!$type->isValidValue($resolvedValueItem)) {
                    $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolve value in %s field', $field->getName())));

                    $listValue = null;
                    break;
                }
                $listValue[] = $type->getKind() != TypeMap::KIND_OBJECT ? $type->serialize($resolvedValueItem) : $resolvedValueItem;
            }

            $value = $listValue;
        } else {
            $value = $preResolvedValue;

            /** hotfix for enum $field->getType()->resolve($preResolvedValue); */ //todo: refactor here
            if ($fieldType->getKind() == TypeMap::KIND_NON_NULL) {
                if (!$fieldType->isValidValue($preResolvedValue)) {
                    $this->resolveValidator->addError(new ResolveException(sprintf('Cannot return null for non-nullable field %s', $astField->getName() . '.' . $field->getName())));
                } elseif (!$fieldType->getNullableType()->isValidValue($preResolvedValue)) {
                    $this->resolveValidator->addError(new ResolveException(sprintf('Not valid value for %s field %s', $fieldType->getNullableType()->getKind(), $field->getName())));
                    $value = null;
                } else {
                    $value = $preResolvedValue;
                }
            } elseif ($fieldType->getKind() != TypeMap::KIND_OBJECT) {
                $value = $fieldType->serialize($preResolvedValue);
            }
        }

        return $value;
    }

    /**
     * @param               $query
     * @param mixed         $contextValue
     * @param AbstractField $field
     *
     * @return null
     */
    protected function processFieldTypeQuery($query, $contextValue, AbstractField $field)
    {
        if (!($resolvedValue = $this->resolveFieldValue($field, $contextValue, $query))) {
            return $resolvedValue;
        }

        /** we probably do not need this if here $type */
//        if (!$this->resolveValidator->validateResolvedValueType($resolvedValue, $field->getType())) {
//            return null;
//        }

        $type = $this->resolveTypeIfAbstract($field->getType(), $resolvedValue);

        return $this->collectTypeResolvedValue($type, $resolvedValue, $query);
    }

    /**
     * @param AbstractField $field
     * @param mixed         $contextValue
     * @param Query         $query
     *
     * @return mixed
     */
    protected function resolveFieldValue(AbstractField $field, $contextValue, $query)
    {
        $type          = $field->getType();
        $resolvedValue = $field->resolve($contextValue, $this->parseArgumentsValues($field, $query), $type);

        return $resolvedValue;
    }

    /**
     * @param AbstractType $type
     * @param              $resolvedValue
     *
     * @return AbstractType|AbstractInterfaceType
     *
     * @throws ResolveException
     */
    protected function resolveTypeIfAbstract(AbstractType $type, $resolvedValue)
    {
        if (TypeService::isAbstractType($type)) {
            /** @var AbstractInterfaceType $type */
            $resolvedType = $type->resolveType($resolvedValue);

            if ($type instanceof AbstractInterfaceType) {
                $this->resolveValidator->assertTypeImplementsInterface($resolvedType, $type);
            } elseif ($type instanceof AbstractUnionType) {
                $this->resolveValidator->assertTypeInUnionTypes($resolvedType, $type);
            }

            return $resolvedType;
        }

        return $type;
    }

    /**
     * @param AbstractType   $fieldType
     * @param mixed          $resolvedValue
     * @param Query|Mutation $query
     * @return array|mixed
     * @throws \Exception
     */
    protected function collectTypeResolvedValue(AbstractType $fieldType, $resolvedValue, $query)
    {
        $value = [];
        if ($fieldType->getKind() == TypeMap::KIND_LIST) {
            foreach ($resolvedValue as $resolvedValueItem) {
                $value[]   = [];
                $index     = count($value) - 1;
                $namedType = $fieldType->getNamedType();
                $namedType = $this->resolveTypeIfAbstract($namedType, $resolvedValueItem);

                $value[$index] = $this->processQueryFields($query, $namedType, $resolvedValueItem, $value[$index]);
            }
        } else {
            $value = $this->processQueryFields($query, $fieldType, $resolvedValue, $value);
        }

        return $value;
    }

    /**
     * @param               $contextValue
     * @param AstField      $astField
     * @param AbstractField $field
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getPreResolvedValue($contextValue, AstField $astField, AbstractField $field)
    {
        $resolved      = false;
        $resolverValue = null;

        if (is_array($contextValue) && array_key_exists($astField->getName(), $contextValue)) {
            $resolverValue = $contextValue[$astField->getName()];
            $resolved      = true;
        } elseif (is_object($contextValue)) {
            $resolverValue = TypeService::getPropertyValue($contextValue, $astField->getName());
            $resolved      = true;
        }

        if ($field->getType()->getNamedType()->getKind() == TypeMap::KIND_SCALAR) {
            $resolved = true;
        }

        if ($field->getConfig()->getResolveFunction()) {
            $resolverValue = $field->resolve($contextValue, $astField->getKeyValueArguments(), $field->getType());
        }

        if (!$resolverValue && !$resolved) {
            throw new \Exception(sprintf('Property "%s" not found in resolve result', $astField->getName()));
        }

        return $resolverValue;

    }

    /**
     * @param $field     AbstractField
     * @param $query     Query
     *
     * @return array
     */
    protected function parseArgumentsValues(AbstractField $field, Query $query)
    {
        $args = [];
        foreach ($query->getArguments() as $argument) {
            if ($configArgument = $field->getConfig()->getArgument($argument->getName())) {
                $args[$argument->getName()] = $configArgument->getType()->parseValue($argument->getValue()->getValue());
            }
        }

        return $args;
    }

    /**
     * @param $query         Query|FragmentInterface
     * @param $queryType     AbstractObjectType|TypeInterface|Field|AbstractType
     * @param $resolvedValue mixed
     * @param $value         array
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function processQueryFields($query, AbstractType $queryType, $resolvedValue, $value)
    {
        foreach ($query->getFields() as $field) {
            if ($field instanceof FragmentInterface) {
                /** @var TypedFragmentReference $fragment */
                $fragment = $field;
                if ($field instanceof FragmentReference) {
                    /** @var Fragment $fragment */
                    $fragment = $this->request->getFragment($field->getName());
                    $this->resolveValidator->assertValidFragmentForField($fragment, $field, $queryType);
                } elseif ($fragment->getTypeName() !== $queryType->getName()) {
                    continue;
                }

                $fragmentValue = $this->processQueryFields($fragment, $queryType, $resolvedValue, $value);
                $value = array_merge(
                    is_array($value) ? $value : [],
                    is_array($fragmentValue) ? $fragmentValue : []
                );
            } elseif ($field->getName() == self::TYPE_NAME_QUERY) {
                $value = $this->collectValue($value, [$field->getAlias() ?: $field->getName() => $queryType->getName()]);
            } else {
                /** need to add check if $queryType is not an objecttype $value */
                $value = $this->collectValue($value, $this->executeQuery($field, $queryType, $resolvedValue));
            }
        }

        return $value;
    }

    protected function collectValue($value, $queryValue)
    {
        if ($queryValue && is_array($queryValue)) {
            $value = array_merge(is_array($value) ? $value : [], $queryValue);
        } else {
            $value = $queryValue;
        }

        return $value;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getResponseData()
    {
        $result = [];

        if (!empty($this->data)) {
            $result['data'] = $this->data;
        }

        $this->mergeErrors($this->resolveValidator);
        if ($this->hasErrors()) {
            $result['errors'] = $this->getErrorsArray();
        }
        $this->clearErrors();
        $this->resolveValidator->clearErrors();
        $this->schemaValidator->clearErrors();

        return $result;
    }
}
