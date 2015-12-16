<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 1:05 AM
*/

namespace Youshido\GraphQL;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Youshido\GraphQL\Definition\SchemaType;
use Youshido\GraphQL\Definition\TypeDefinitionType;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractEnumType;
use Youshido\GraphQL\Type\Object\InputObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ResolveException;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidatorInterface;

class Processor
{

    const TYPE_NAME_QUERY = '__typename';

    /** @var  array */
    protected $data;

    /** @var ResolveValidatorInterface */
    protected $resolveValidator;

    /** @var Schema */
    protected $schema;

    /** @var PropertyAccessor */
    protected $propertyAccessor;

    /** @var Request */
    protected $request;

    public function __construct(ResolveValidatorInterface $validator)
    {
        $this->resolveValidator = $validator;

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function processQuery($queryString, $variables = [])
    {
        $this->resolveValidator->clearErrors();
        $this->data = [];

        try {
            $this->parseAndCreateRequest($queryString, $variables);

            if ($this->request->hasQueries()) {
                foreach ($this->request->getQueries() as $query) {
                    if ($queryResult = $this->executeQuery($query, $this->getSchema()->getQueryType())) {
                        $this->data = array_merge($this->data, $queryResult);
                    };
                }
            }

            if ($this->request->hasMutations()) {
                foreach ($this->request->getMutations() as $mutation) {
                    if ($mutationResult = $this->executeMutation($mutation, $this->getSchema()->getMutationType())) {
                        $this->data = array_merge($this->data, $mutationResult);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->resolveValidator->clearErrors();

            $this->resolveValidator->addError($e);
        }
    }

    /**
     * @param Mutation        $mutation
     * @param InputObjectType $objectType
     *
     * @return array|bool|mixed
     */
    protected function executeMutation($mutation, $objectType)
    {
        if (!$this->checkFieldExist($objectType, $mutation)) {

            return null;
        }

        /** @var Field $field */
        $field = $objectType->getConfig()->getField($mutation->getName());

        if (!$this->resolveValidator->validateArguments($field, $mutation, $this->request)) {
            return null;
        }

        $alias         = $mutation->hasAlias() ? $mutation->getAlias() : $mutation->getName();
        $resolvedValue = $this->resolveValue($field, null, $mutation);

        if (!$this->resolveValidator->validateResolvedValue($resolvedValue, $field->getType()->getKind())) {
            $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolved value for mutation "%s"', $field->getType()->getName())));

            return [$alias => null];
        }

        $value = null;
        if ($mutation->hasFields()) {
            $outputType = $field->getType()->getConfig()->getOutputType();

            $value = $this->processQueryFields($mutation, $outputType, $resolvedValue, []);
        }

        return [$alias => $value];
    }

    protected function parseAndCreateRequest($query, $variables = [])
    {
        $parser = new Parser();

        $parser->setSource($query);
        $data = $parser->parse();

        $this->request = new Request($data);
        $this->request->setVariables($variables);
    }

    /**
     * @param Query|Field $query
     * @param ObjectType  $currentLevelSchema
     * @param null        $contextValue
     * @return array|bool|mixed
     */
    protected function executeQuery($query, $currentLevelSchema, $contextValue = null)
    {
        if (!$this->checkFieldExist($currentLevelSchema, $query)) {
            return null;
        }

        /** @var Field $field */
        $field = $currentLevelSchema->getConfig()->getField($query->getName());
        if (get_class($query) == 'Youshido\GraphQL\Parser\Ast\Field') {
            $alias            = $query->getName();
            $preResolvedValue = $this->getPreResolvedValue($contextValue, $query);

            if ($field->getConfig()->getType()->getKind() == TypeMap::KIND_LIST) {
                if(!is_array($preResolvedValue)){
                    $value = null;
                    $this->resolveValidator->addError(new ResolveException('Not valid resolve value for list type'));
                }


                $listValue = [];
                foreach ($preResolvedValue as $resolvedValueItem) {
                    /** @var TypeInterface $type */
                    $type = $field->getType()->getConfig()->getItem();

                    if ($type->getKind() == TypeMap::KIND_ENUM) {
                        /** @var $type AbstractEnumType */
                        if(!$type->isValidValue($resolvedValueItem)) {
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
                    if(!$field->getType()->isValidValue($preResolvedValue)) {
                        $this->resolveValidator->addError(new ResolveException('Not valid value for enum type'));
                        $value = null;
                    } else {
                        $value = $field->getType()->resolve($preResolvedValue);
                    }
                } else {
                    $value = $field->getType()->serialize($preResolvedValue);
                }
            }
        } else {
            if (!$this->resolveValidator->validateArguments($field, $query, $this->request)) {
                return null;
            }

            $resolvedValue = $this->resolveValue($field, $contextValue, $query);
            $alias         = $query->hasAlias() ? $query->getAlias() : $query->getName();

            if (!$this->resolveValidator->validateResolvedValue($resolvedValue, $field->getType()->getKind())) {
                $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolved value for query "%s"', $field->getType()->getName())));

                return [$alias => null];
            }

            $value = [];
            if ($resolvedValue) {
                if ($field->getType()->getKind() == TypeMap::KIND_LIST) {
                    foreach ($resolvedValue as $resolvedValueItem) {
                        $value[] = [];
                        $index   = count($value) - 1;

                        if(in_array($field->getConfig()->getType()->getConfig()->getItem()->getKind(), [TypeMap::KIND_UNION, TypeMap::KIND_INTERFACE]) ) {
                            $type = $field->getConfig()->getType()->getConfig()->getItemConfig()->resolveType($resolvedValueItem);
                        } else {
                            $type = $field->getType();
                        }

                        $value[$index] = $this->processQueryFields($query, $type, $resolvedValueItem, $value[$index]);
                    }
                } else {
                    $value = $this->processQueryFields($query, $field->getType(), $resolvedValue, $value);
                }
            } else {
                $value = $resolvedValue;
            }
        }

        return [$alias => $value];
    }

    /**
     * @param $objectType InputObjectType|ObjectType
     * @param $query      Mutation|Query
     * @return null
     */
    private function checkFieldExist($objectType, $query)
    {
        if (!$objectType->getConfig()->hasField($query->getName())) {
            $this->resolveValidator->addError(new ResolveException(sprintf('Field "%s" not found in schema', $query->getName())));

            return false;
        }

        return true;
    }

    /**
     * @param $value
     * @param $query Field
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getPreResolvedValue($value, $query)
    {
        if (is_array($value)) {
            if (array_key_exists($query->getName(), $value)) {
                return $value[$query->getName()];
            } else {
                throw new \Exception('Not found in resolve result', $query->getName());
            }
        } elseif (is_object($value)) {
            return $this->propertyAccessor->getValue($value, $query->getName());
        }

        return $value;
    }

    /**
     * @param $field        Field
     * @param $contextValue mixed
     * @param $query        Query
     *
     * @return mixed
     */
    protected function resolveValue($field, $contextValue, $query)
    {
        $resolvedValue = $field->getConfig()->resolve($contextValue, $this->parseArgumentsValues($field, $query));

        if(in_array($field->getType()->getKind(), [TypeMap::KIND_UNION, TypeMap::KIND_INTERFACE])){
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

        $args      = [];
        foreach ($query->getArguments() as $argument) {
            $args[$argument->getName()] = $field->getConfig()->getArgument($argument->getName())->getType()->parseValue($argument->getValue()->getValue());
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
            } else if($field->getName() == self::TYPE_NAME_QUERY) {
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
            $value = array_merge($value, $queryValue);
        } else {
            $value = $queryValue;
        }

        return $value;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;

        $__schema = new SchemaType();
        $__schema->setSchema($schema);

        $__type = new TypeDefinitionType();
        $__type->setSchema($schema);

        $this->schema->addQuery('__schema', $__schema);
        $this->schema->addQuery('__type', $__type);
    }

    /**
     * @return ResolveValidatorInterface
     */
    public function getResolveValidator()
    {
        return $this->resolveValidator;
    }

    public function getResponseData()
    {
        $result = [];

        if (!empty($this->data)) {
            $result['data'] = $this->data;
        }

        if ($this->resolveValidator->hasErrors()) {
            $result['errors'] = $this->resolveValidator->getErrorsArray();
        }

        return $result;
    }
}
