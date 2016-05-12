<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 1:05 AM
*/

namespace Youshido\GraphQL;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\QueryType;
use Youshido\GraphQL\Introspection\SchemaType;
use Youshido\GraphQL\Introspection\TypeDefinitionType;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;
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

        $__schema = new SchemaType();
        $__schema->setSchema($schema);

        $__type = new TypeDefinitionType();

        $this->schema->addQuery('__schema', $__schema);
        $this->schema->addQuery('__type', $__type);
    }

    public function processRequest($payload, $variables = [])
    {
        if (!$this->getSchema()) {
            $this->addError(new ConfigurationException('You have to set GraphQL Schema to process'));
        }
        if (empty($payload) || $this->hasErrors()) return $this;

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
     * @param Query|Field          $query
     * @param ObjectType|QueryType $currentLevelSchema
     * @param null                 $contextValue
     * @return array|bool|mixed
     */
    protected function executeQuery($query, $currentLevelSchema, $contextValue = null)
    {
        if (!$this->resolveValidator->checkFieldExist($currentLevelSchema, $query)) {
            return null;
        }

        /** @var Field $field */
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
        if (!$currentLevelSchema) throw new ConfigurationException('There is no mutation ' . $mutation->getName());

        if (!$this->resolveValidator->checkFieldExist($currentLevelSchema, $mutation)) {
            return null;
        }

        /** @var Field $field */
        $field = $currentLevelSchema->getConfig()->getField($mutation->getName());
        $alias = $mutation->getAlias() ?: $mutation->getName();

        if (!$this->resolveValidator->validateArguments($field, $mutation, $this->request)) {
            return null;
        }

        $resolvedValue = $this->resolveValue($field, null, $mutation);

        if (!$this->resolveValidator->validateResolvedValue($resolvedValue, $field->getType())) {
            $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolved value for mutation "%s"', $field->getType()->getName())));

            return [$alias => null];
        }

        $value = $resolvedValue;
        if ($mutation->hasFields()) {
            if ($field->getType()->isAbstractType()) {
                $outputType = $field->getType()->getConfig()->resolveType($resolvedValue);
            } else {
                /** @var AbstractType $outputType */
                $outputType = $field->getType();
            }

            $value = $this->collectListOrSingleValue($outputType, $resolvedValue, $mutation);
        }

        return [$alias => $value];
    }

    /**
     * @param AstField $astField
     * @param mixed    $contextValue
     * @param Field    $field
     * @return array|mixed|null
     * @throws \Exception
     */
    protected function processAstFieldQuery(AstField $astField, $contextValue, Field $field)
    {
        $value            = null;
        $preResolvedValue = $this->getPreResolvedValue($contextValue, $astField, $field);

        if ($field->getType()->getKind() == TypeMap::KIND_LIST) {
            if (!is_array($preResolvedValue)) {
                $this->resolveValidator->addError(new ResolveException('Not valid resolve value for list type'));

                return null;
            }

            $listValue = [];
            foreach ($preResolvedValue as $resolvedValueItem) {
                /** @var TypeInterface $type */
                $type = $field->getType()->getConfig()->getItem();

                if ($type->getKind() == TypeMap::KIND_ENUM) {
                    /** @var $type AbstractEnumType */
                    if (!$type->isValidValue($resolvedValueItem)) {
                        $this->resolveValidator->addError(new ResolveException('Not valid value for enum type'));

                        $listValue = null;
                        break;
                    }

                    $listValue[] = $type->resolve($resolvedValueItem);
                } else {
                    /** @var AbstractScalarType $type */
                    $listValue[] = $type->serialize($preResolvedValue);
                }
            }

            $value = $listValue;
        } else {
            if ($field->getType()->getKind() == TypeMap::KIND_ENUM) {
                if (!$field->getType()->isValidValue($preResolvedValue)) {
                    $this->resolveValidator->addError(new ResolveException(sprintf('Not valid value for %s type', ($field->getType()->getKind()))));
                    $value = null;
                } else {
                    $value = $preResolvedValue;
                    /** $field->getType()->resolve($preResolvedValue); */
                }
            } elseif ($field->getType()->getKind() == TypeMap::KIND_NON_NULL) {
                if (!$field->getType()->isValidValue($preResolvedValue)) {
                    $this->resolveValidator->addError(new ResolveException(sprintf('Cannot return null for non-nullable field %s', $astField->getName() . '.' . $field->getName())));
                } elseif (!$field->getType()->getNullableType()->isValidValue($preResolvedValue)) {
                    $this->resolveValidator->addError(new ResolveException(sprintf('Not valid value for %s field %s', $field->getType()->getNullableType()->getKind(), $field->getName())));
                    $value = null;
                } else {
                    $value = $preResolvedValue;
                }
            } else {
                $value = $field->getType()->serialize($preResolvedValue);
            }
        }

        return $value;
    }

    /**
     * @param       $query
     * @param mixed $contextValue
     * @param Field $field
     * @return null
     * @throws \Exception
     */
    protected function processFieldTypeQuery($query, $contextValue, $field)
    {
        if (!($resolvedValue = $this->resolveValue($field, $contextValue, $query))) {
            return $resolvedValue;
        }

        if (!$this->resolveValidator->validateResolvedValue($resolvedValue, $field->getType())) {
            $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolved value for query "%s"', $field->getType()->getName())));

            return null;
        }

        return $this->collectListOrSingleValue($field->getType(), $resolvedValue, $query);
    }

    /**
     * @param AbstractType   $fieldType
     * @param mixed          $resolvedValue
     * @param Query|Mutation $query
     * @return array|mixed
     * @throws \Exception
     */
    protected function collectListOrSingleValue(AbstractType $fieldType, $resolvedValue, $query)
    {
        $value = [];
        if ($fieldType->getKind() == TypeMap::KIND_LIST) {
            foreach ($resolvedValue as $resolvedValueItem) {
                $value[]   = [];
                $index     = count($value) - 1;
                $namedType = $fieldType->getNamedType();

                if ($namedType->isAbstractType()) {
                    $resolvedType = $namedType->getConfig()->resolveType($resolvedValueItem);
                    if ($namedType instanceof AbstractInterfaceType) {
                        $this->resolveValidator->assertTypeImplementsInterface($resolvedType, $namedType);
                    }
                    $namedType = $resolvedType;
                }

                $value[$index] = $this->processQueryFields($query, $namedType, $resolvedValueItem, $value[$index]);
            }
        } else {
            $value = $this->processQueryFields($query, $fieldType, $resolvedValue, $value);
        }

        return $value;
    }

    /**
     * @param          $value
     * @param AstField $astField
     * @param Field    $field
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getPreResolvedValue($value, AstField $astField, Field $field)
    {
        $resolved      = false;
        $resolverValue = null;

        if (is_array($value) && array_key_exists($astField->getName(), $value)) {
            $resolverValue = $value[$astField->getName()];
            $resolved      = true;
        } elseif (is_object($value)) {
            try {
                $resolverValue = $this->getPropertyValue($value, $astField->getName());
                $resolved      = true;
            } catch (\Exception $e) {
            }
        } elseif ($field->getNamedType()->getKind() == TypeMap::KIND_SCALAR) {
            $resolved = true;
        }

        if ($resolved) {
            if ($field->getConfig() && ($field->getConfig()->issetResolve())) {
                $resolverValue = $field->resolve($resolverValue, $astField->getKeyValueArguments(), $field->getType());
            }

            return $resolverValue;
        }

        throw new \Exception(sprintf('Property "%s" not found in resolve result', $astField->getName()));
    }

    protected function getPropertyValue($data, $path)
    {
        if (is_object($data)) {
            $getter = 'get' . $this->classify($path);

            return is_callable([$data, $getter]) ? $data->$getter() : null;
        } elseif (is_array($data)) {
            return array_key_exists($path, $data) ? $data[$path] : null;
        }

        return null;
    }

    protected function classify($text)
    {
        $text       = explode(' ', str_replace(['_', '/', '-', '.'], ' ', $text));
        $textLength = count($text);
        for ($i = 0; $i < $textLength; $i++) {
            $text[$i] = ucfirst($text[$i]);
        }
        $text = ucfirst(implode('', $text));

        return $text;
    }

    /**
     * @param Field $field
     * @param mixed $contextValue
     * @param Query $query
     *
     * @return mixed
     */
    protected function resolveValue($field, $contextValue, $query)
    {
        $resolvedValue = $field->resolve($contextValue, $this->parseArgumentsValues($field, $query), $field->getType());

        if ($field->getType()->isAbstractType()) {
            $resolvedType = $field->getType()->resolveType($resolvedValue);
            $field->setType($resolvedType);
        }

        return $resolvedValue;
    }

    /**
     * @param $field     Field
     * @param $query     Query
     *
     * @return array
     */
    public function parseArgumentsValues($field, $query)
    {
        if ($query instanceof \Youshido\GraphQL\Parser\Ast\Field) {
            return [];
        }

        $args = [];
        foreach ($query->getArguments() as $argument) {
            if ($configArgument = $field->getConfig()->getArgument($argument->getName())) {
                $args[$argument->getName()] = $configArgument->getType()->parseValue($argument->getValue()->getValue());
            }
        }

        return $args;
    }

    /**
     * @param $query         Query
     * @param $queryType     ObjectType|TypeInterface|Field
     * @param $resolvedValue mixed
     * @param $value         array
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function processQueryFields($query, $queryType, $resolvedValue, $value)
    {
        foreach ($query->getFields() as $field) {
            if ($field instanceof FragmentReference) {
                if (!$fragment = $this->request->getFragment($field->getName())) {
                    throw new \Exception(sprintf('Fragment reference "%s" not found', $field->getName()));
                }

                if ($fragment->getModel() !== $queryType->getName()) {
                    throw new \Exception(sprintf('Fragment reference "%s" not found on model "%s"', $field->getName(), $queryType->getName()));
                }

                foreach ($fragment->getFields() as $fragmentField) {
                    $value = $this->collectValue($value, $this->executeQuery($fragmentField, $queryType, $resolvedValue));
                }
            } elseif ($field instanceof TypedFragmentReference) {
                if ($field->getTypeName() !== $queryType->getName()) {
                    continue;
                }

                foreach ($field->getFields() as $fragmentField) {
                    $value = $this->collectValue($value, $this->executeQuery($fragmentField, $queryType, $resolvedValue));
                }
            } elseif ($field->getName() == self::TYPE_NAME_QUERY) {
                $value = $this->collectValue($value, [$field->getAlias() ?: $field->getName() => $queryType->getName()]);
            } else {
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
