<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 1:05 AM
*/

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Field\SchemaField;
use Youshido\GraphQL\Introspection\Field\TypeDefinitionField;
use Youshido\GraphQL\Parser\Ast\Field as FieldAst;
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
use Youshido\GraphQL\Validator\Exception\ConfigurationException;
use Youshido\GraphQL\Validator\Exception\ResolveException;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidatorInterface;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;

class Processor
{

    const TYPE_NAME_QUERY = '__typename';

    /** @var  array */
    protected $data;

    /** @var ResolveValidatorInterface */
    protected $resolveValidator;

    /** @var ExecutionContext */
    protected $executionContext;

    public function __construct(AbstractSchema $schema)
    {
        (new SchemaValidator())->validate($schema);

        $this->introduceIntrospectionFields($schema);
        $this->executionContext = new ExecutionContext();
        $this->executionContext->setSchema($schema);

        $this->resolveValidator = new ResolveValidator($this->executionContext);
    }

    public function introduceIntrospectionFields(AbstractSchema $schema)
    {
        $schemaField = new SchemaField();
        $schemaField->setSchema($schema);

        $schema->addQueryField($schemaField);
        $schema->addQueryField(new TypeDefinitionField());
    }

    public function processPayload($payload, $variables = [])
    {
        if ($this->executionContext->hasErrors()) {
            $this->executionContext->clearErrors();
        }

        $this->data = [];

        try {
            $this->parseAndCreateRequest($payload, $variables);

            foreach ($this->executionContext->getRequest()->getQueries() as $query) {
                if ($queryResult = $this->executeQuery($query, $this->executionContext->getSchema()->getQueryType())) {
                    $this->data = array_merge($this->data, $queryResult);
                };
            }

            foreach ($this->executionContext->getRequest()->getMutations() as $mutation) {
                if ($mutationResult = $this->executeMutation($mutation, $this->executionContext->getSchema()->getMutationType())) {
                    $this->data = array_merge($this->data, $mutationResult);
                }
            }
        } catch (\Exception $e) {
            $this->executionContext->addError($e);
        }

        return $this;
    }

    protected function parseAndCreateRequest($query, $variables = [])
    {
        $parser = new Parser();

        $data = $parser->parse($query);
        $this->executionContext->setRequest(new Request($data, $variables));
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

        if ($query instanceof FieldAst) {
            $value = $this->processFieldAstQuery($query, $contextValue, $field);
        } else {
            if (!$this->resolveValidator->validateArguments($field, $query, $this->executionContext->getRequest())) {
                return null;
            }

            $value = $this->processFieldTypeQuery($query, $contextValue, $field);

        }

        return [$alias => $value];
    }

    /**
     * @param Mutation   $mutation
     * @param AbstractObjectType $currentLevelSchema
     * @return array|null
     * @throws ConfigurationException
     */
    protected function executeMutation(Mutation $mutation, AbstractObjectType $currentLevelSchema)
    {
        if (!$this->resolveValidator->objectHasField($currentLevelSchema, $mutation)) {
            return null;
        }

        /** @var AbstractField $field */
        $field     = $currentLevelSchema->getConfig()->getField($mutation->getName());
        $alias     = $mutation->getAlias() ?: $mutation->getName();
        $fieldType = $field->getType();

        if (!$this->resolveValidator->validateArguments($field, $mutation, $this->executionContext->getRequest())) {
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
     * @param FieldAst      $FieldAst
     * @param mixed         $contextValue
     * @param AbstractField $field
     *
     * @return array|mixed|null
     */
    protected function processFieldAstQuery(FieldAst $FieldAst, $contextValue, AbstractField $field)
    {
        $value            = null;
        $fieldType        = $field->getType();
        $preResolvedValue = $this->getPreResolvedValue($contextValue, $FieldAst, $field);

        if ($preResolvedValue && !$this->resolveValidator->validateResolvedValueType($preResolvedValue, $field->getType())) {
            return null;
        }

        if ($fieldType->getKind() == TypeMap::KIND_LIST) {
            $listValue = [];
            foreach ($preResolvedValue as $resolvedValueItem) {
                $type = $fieldType->getNamedType();

                if (!$type->isValidValue($resolvedValueItem)) {
                    $this->executionContext->addError(new ResolveException(sprintf('Not valid resolve value in %s field', $field->getName())));

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
                    $this->executionContext->addError(new ResolveException(sprintf('Cannot return null for non-nullable field %s', $FieldAst->getName() . '.' . $field->getName())));
                } elseif (!$fieldType->getNullableType()->isValidValue($preResolvedValue)) {
                    $this->executionContext->addError(new ResolveException(sprintf('Not valid value for %s field %s', $fieldType->getNullableType()->getKind(), $field->getName())));
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

        if (!$this->resolveValidator->validateResolvedValueType($resolvedValue, $field->getType())) {
            return null;
        }

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
    protected function resolveFieldValue(AbstractField $field, $contextValue, Query $query)
    {
        $resolveInfo = new ResolveInfo($field, $query->getFields(), $field->getType(), $this->executionContext);

        return $field->resolve($contextValue, $this->parseArgumentsValues($field, $query), $resolveInfo);
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
            } else {
                /** @var AbstractUnionType $type */
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
     * @param FieldAst      $fieldAst
     * @param AbstractField $field
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getPreResolvedValue($contextValue, FieldAst $fieldAst, AbstractField $field)
    {
        $resolved      = false;
        $resolverValue = null;

        if (is_array($contextValue) && array_key_exists($fieldAst->getName(), $contextValue)) {
            $resolverValue = $contextValue[$fieldAst->getName()];
            $resolved      = true;
        } elseif (is_object($contextValue)) {
            $resolverValue = TypeService::getPropertyValue($contextValue, $fieldAst->getName());
            $resolved      = true;
        }

        if ($field->getType()->getNamedType()->getKind() == TypeMap::KIND_SCALAR) {
            $resolved = true;
        }

        if ($field->getConfig()->getResolveFunction()) {
            $resolveInfo   = new ResolveInfo($field, [$fieldAst], $field->getType(), $this->executionContext);
            $resolverValue = $field->resolve($resolved ? $resolverValue : $contextValue, $fieldAst->getKeyValueArguments(), $resolveInfo);
        }

        if (!$resolverValue && !$resolved) {
            throw new \Exception(sprintf('Property "%s" not found in resolve result', $fieldAst->getName()));
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
                    $fragment = $this->executionContext->getRequest()->getFragment($field->getName());
                    $this->resolveValidator->assertValidFragmentForField($fragment, $field, $queryType);
                } elseif ($fragment->getTypeName() !== $queryType->getName()) {
                    continue;
                }

                $fragmentValue = $this->processQueryFields($fragment, $queryType, $resolvedValue, $value);
                $value         = array_merge(
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


    public function getResponseData()
    {
        $result = [];

        if (!empty($this->data)) {
            $result['data'] = $this->data;
        }

        if ($this->executionContext->hasErrors()) {
            $result['errors'] = $this->executionContext->getErrorsArray();
        }

        $this->executionContext->clearErrors();

        return $result;
    }
}
