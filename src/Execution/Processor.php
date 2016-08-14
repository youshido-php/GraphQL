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
use Youshido\GraphQL\Execution\Visitor\AbstractQueryVisitor;
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
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
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

    /** @var int */
    protected $maxComplexity;

    public function __construct(AbstractSchema $schema)
    {
        (new SchemaValidator())->validate($schema);

        $this->introduceIntrospectionFields($schema);
        $this->executionContext = new ExecutionContext();
        $this->executionContext->setSchema($schema);

        $this->resolveValidator = new ResolveValidator($this->executionContext);
    }


    public function processPayload($payload, $variables = [], $reducers = [])
    {
        if ($this->executionContext->hasErrors()) {
            $this->executionContext->clearErrors();
        }

        $this->data = [];

        try {
            $this->parseAndCreateRequest($payload, $variables);

            $queryType    = $this->executionContext->getSchema()->getQueryType();
            $mutationType = $this->executionContext->getSchema()->getMutationType();

            if ($this->maxComplexity) {
                $reducers[] = new \Youshido\GraphQL\Execution\Visitor\MaxComplexityQueryVisitor($this->maxComplexity);
            }

            $this->reduceQuery($queryType, $mutationType, $reducers);

            foreach ($this->executionContext->getRequest()->getOperationsInOrder() as $operation) {
                if ($operationResult = $this->executeOperation($operation, $operation instanceof Mutation ? $mutationType : $queryType)) {
                    $this->data = array_merge($this->data, $operationResult);
                };
            }

        } catch (\Exception $e) {
            $this->executionContext->addError($e);
        }

        return $this;
    }

    protected function parseAndCreateRequest($payload, $variables = [])
    {
        if (empty($payload)) {
            throw new \Exception('Must provide an operation.');
        }
        $parser = new Parser();

        $data = $parser->parse($payload);
        $this->executionContext->setRequest(new Request($data, $variables));
    }

    /**
     * @param Query|Field        $query
     * @param AbstractObjectType $currentLevelSchema
     * @return array|bool|mixed
     */
    protected function executeOperation(Query $query, $currentLevelSchema)
    {
        if (!$this->resolveValidator->objectHasField($currentLevelSchema, $query)) {
            return null;
        }

        /** @var AbstractField $field */
        $operationField = $currentLevelSchema->getField($query->getName());
        $alias          = $query->getAlias() ?: $query->getName();

        if (!$this->resolveValidator->validateArguments($operationField, $query, $this->executionContext->getRequest())) {
            return null;
        }

        return [$alias => $this->processQueryAST($query, $operationField)];
    }

    /**
     * @param Query         $query
     * @param AbstractField $field
     * @param               $contextValue
     * @return array|mixed|null
     */
    protected function processQueryAST(Query $query, AbstractField $field, $contextValue = null)
    {
        if (!$this->resolveValidator->validateArguments($field, $query, $this->executionContext->getRequest())) {
            return null;
        }

        $resolvedValue = $this->resolveFieldValue($field, $contextValue, $query);

        if (!$this->resolveValidator->isValidValueForField($field, $resolvedValue)) {
            return null;
        }

        return $this->collectValueForQueryWithType($query, $field->getType(), $resolvedValue);
    }

    /**
     * @param Query|Mutation $query
     * @param AbstractType   $fieldType
     * @param mixed          $resolvedValue
     * @return array|mixed
     */
    protected function collectValueForQueryWithType(Query $query, AbstractType $fieldType, $resolvedValue)
    {
        if (is_null($resolvedValue)) {
            return null;
        }

        $fieldType = $this->resolveValidator->resolveTypeIfAbstract($fieldType, $resolvedValue);
        $value = [];

        if ($fieldType->getKind() == TypeMap::KIND_LIST) {
            if (!$this->resolveValidator->hasArrayAccess($resolvedValue)) return null;
            foreach ($resolvedValue as $resolvedValueItem) {
                $value[] = [];
                $index   = count($value) - 1;


                $namedType = $fieldType->getNamedType();
                $namedType = $this->resolveValidator->resolveTypeIfAbstract($namedType, $resolvedValueItem);
                if (!$namedType->isValidValue($resolvedValueItem)) {
                    $this->executionContext->addError(new ResolveException(sprintf('Not valid resolve value in %s field', $query->getName())));
                    $value[$index] = null;
                    continue;
                }

                $value[$index] = $this->processQueryFields($query, $namedType, $resolvedValueItem, $value[$index]);
            }
        } else {
            if (!$query->hasFields()) {
                return $this->getOutputValue($fieldType, $resolvedValue);
            }

            $value = $this->processQueryFields($query, $fieldType, $resolvedValue, $value);
        }

        return $value;
    }

    /**
     * @param FieldAst      $fieldAst
     * @param AbstractField $field
     *
     * @param mixed         $contextValue
     * @return array|mixed|null
     * @throws ResolveException
     * @throws \Exception
     */
    protected function processFieldAST(FieldAst $fieldAst, AbstractField $field, $contextValue)
    {
        $value            = null;
        $fieldType        = $field->getType();
        $preResolvedValue = $this->getPreResolvedValue($contextValue, $fieldAst, $field);

        if ($fieldType->getKind() == TypeMap::KIND_LIST) {
            $listValue = [];
            foreach ($preResolvedValue as $resolvedValueItem) {
                $type = $fieldType->getNamedType();

                if (!$type->isValidValue($resolvedValueItem)) {
                    $this->executionContext->addError(new ResolveException(sprintf('Not valid resolve value in %s field', $field->getName())));

                    $listValue = null;
                    break;
                }
                $listValue[] = $this->getOutputValue($type, $resolvedValueItem);
            }

            $value = $listValue;
        } else {
            $value = $this->getFieldValidatedValue($field, $preResolvedValue);
        }

        return $value;
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

        if ($resolveFunc = $field->getConfig()->getResolveFunction()) {
            return $resolveFunc($contextValue, $this->parseArgumentsValues($field, $query), $resolveInfo);
        } elseif ($propertyValue = TypeService::getPropertyValue($contextValue, $field->getName())) {
            return $propertyValue;
        } else {
            return $field->resolve($contextValue, $this->parseArgumentsValues($field, $query), $resolveInfo);
        }
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

        if ($resolveFunction = $field->getConfig()->getResolveFunction()) {
            $resolveInfo = new ResolveInfo($field, [$fieldAst], $field->getType(), $this->executionContext);

            if (!$this->resolveValidator->validateArguments($field, $fieldAst, $this->executionContext->getRequest())) {
                throw new \Exception(sprintf('Not valid arguments for the field "%s"', $fieldAst->getName()));

            } else {
                $resolverValue = $resolveFunction($resolved ? $resolverValue : $contextValue, $fieldAst->getKeyValueArguments(), $resolveInfo);
                $resolved      = true;
            }

        }

        if (is_array($contextValue) && array_key_exists($fieldAst->getName(), $contextValue)) {
            $resolverValue = $contextValue[$fieldAst->getName()];
            $resolved      = true;
        } elseif (is_object($contextValue)) {
            $resolverValue = TypeService::getPropertyValue($contextValue, $fieldAst->getName());
            $resolved      = true;
        }

        if (!$resolved && $field->getType()->getNamedType()->getKind() == TypeMap::KIND_SCALAR) {
            $resolved = true;
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
        if ($queryType instanceof AbstractScalarType && !$query->hasFields()) {
            return $this->getOutputValue($queryType, $resolvedValue);
        }

        foreach ($query->getFields() as $fieldAst) {
            $fieldResolvedValue = null;

            if ($fieldAst instanceof FragmentInterface) {
                /** @var TypedFragmentReference $fragment */
                $fragment = $fieldAst;
                if ($fieldAst instanceof FragmentReference) {
                    /** @var Fragment $fragment */
                    $fragment = $this->executionContext->getRequest()->getFragment($fieldAst->getName());
                    $this->resolveValidator->assertValidFragmentForField($fragment, $fieldAst, $queryType);
                } elseif ($fragment->getTypeName() !== $queryType->getName()) {
                    continue;
                }

                $fragmentValue      = $this->processQueryFields($fragment, $queryType, $resolvedValue, $value);
                $fieldResolvedValue = is_array($fragmentValue) ? $fragmentValue : [];
            } else {
                $alias       = $fieldAst->getAlias() ?: $fieldAst->getName();
                $currentType = $queryType->getNullableType();

                if ($fieldAst->getName() == self::TYPE_NAME_QUERY) {
                    $fieldResolvedValue = [$alias => $queryType->getName()];
                } else {
                    if (!$this->resolveValidator->objectHasField($currentType, $fieldAst)) {
                        $fieldResolvedValue = null;
                    } else {
                        if ($fieldAst instanceof Query) {
                            $queryAst           = $currentType->getField($fieldAst->getName());
                            $fieldValue         = $queryAst ? $this->processQueryAST($fieldAst, $queryAst, $resolvedValue) : null;
                            $fieldResolvedValue = [$alias => $fieldValue];
                        } elseif ($fieldAst instanceof FieldAst) {
                            $fieldResolvedValue = [
                                $alias => $this->processFieldAST($fieldAst, $currentType->getField($fieldAst->getName()), $resolvedValue)
                            ];
                        }
                    }


                }
            }

            $value = $this->collectValue($value, $fieldResolvedValue);
        }

        return $value;
    }

    protected function getFieldValidatedValue(AbstractField $field, $value)
    {
        return ($this->resolveValidator->isValidValueForField($field, $value)) ? $this->getOutputValue($field->getType(), $value) : null;
    }

    protected function getOutputValue(AbstractType $type, $value)
    {
        return in_array($type->getKind(), [TypeMap::KIND_OBJECT, TypeMap::KIND_NON_NULL]) ? $value : $type->serialize($value);
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

    protected function introduceIntrospectionFields(AbstractSchema $schema)
    {
        $schemaField = new SchemaField();
        $schemaField->setSchema($schema);

        $schema->addQueryField($schemaField);
        $schema->addQueryField(new TypeDefinitionField());
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

        return $result;
    }

    public function setMaxComplexity($max)
    {
        $this->maxComplexity = $max;
    }

    /**
     * @param AbstractType $queryType
     * @param AbstractType $mutationType
     * @param array        $reducers
     */
    protected function reduceQuery($queryType, $mutationType, array $reducers)
    {
        foreach ($reducers as $reducer) {
            foreach ($this->executionContext->getRequest()->getOperationsInOrder() as $operation) {
                $this->doVisit($operation, $operation instanceof Mutation ? $mutationType : $queryType, $reducer);
            }
        }
    }

    /**
     * @param Query                $query
     * @param AbstractType         $currentLevelSchema
     * @param AbstractQueryVisitor $reducer
     */
    protected function doVisit(Query $query, $currentLevelSchema, $reducer)
    {
        if (!($currentLevelSchema instanceof AbstractObjectType) || !$currentLevelSchema->hasField($query->getName())) {
            return;
        }

        if ($operationField = $currentLevelSchema->getField($query->getName())) {

            $coroutine = $this->walkQuery($query, $operationField);

            if ($results = $coroutine->current()) {
                $queryCost = 0;
                while ($results) {
                    // initial values come from advancing the generator via ->current, subsequent values come from ->send()
                    list($queryField, $astField, $childCost) = $results;

                    /**
                     * @var Query|FieldAst $queryField
                     * @var Field          $astField
                     */
                    $cost = $reducer->visit($queryField->getKeyValueArguments(), $astField->getConfig(), $childCost);
                    $queryCost += $cost;
                    $results = $coroutine->send($cost);
                }
            }
        }
    }

    /**
     * Coroutine to walk the query and schema in DFS manner and yield a tuple of (queryNode, schemaNode, childScore)
     *
     * childScore costs are accumulated via values sent into the coroutine.
     *
     * @param Query|Field|FragmentInterface $queryNode
     * @param AbstractField                 $currentLevelAST
     *
     * @return \Generator
     */
    protected function walkQuery($queryNode, AbstractField $currentLevelAST)
    {
        $childrenScore = 0;
        if (!($queryNode instanceof FieldAst)) {
            foreach ($queryNode->getFields() as $queryField) {
                if ($queryField instanceof FragmentInterface) {
                    if ($queryField instanceof FragmentReference) {
                        $queryField = $this->executionContext->getRequest()->getFragment($queryField->getName());
                    }
                    // the next 7 lines are essentially equivalent to `yield from $this->walkQuery(...)` in PHP7.
                    // for backwards compatibility this is equivalent.
                    // This pattern is repeated multiple times in this function, and unfortunately cannot be extracted or
                    // made less verbose.
                    $gen  = $this->walkQuery($queryField, $currentLevelAST);
                    $next = $gen->current();
                    while ($next) {
                        $received = (yield $next);
                        $childrenScore += (int)$received;
                        $next = $gen->send($received);
                    }
                } else {
                    $fieldType = $currentLevelAST->getType()->getNamedType();
                    if ($fieldType instanceof AbstractUnionType) {
                        foreach ($fieldType->getTypes() as $unionFieldType) {
                            if ($fieldAst = $unionFieldType->getField($queryField->getName())) {
                                $gen  = $this->walkQuery($queryField, $fieldAst);
                                $next = $gen->current();
                                while ($next) {
                                    $received = (yield $next);
                                    $childrenScore += (int)$received;
                                    $next = $gen->send($received);
                                }
                            }
                        }
                    } elseif ($fieldType instanceof AbstractObjectType && $fieldAst = $fieldType->getField($queryField->getName())) {
                        $gen  = $this->walkQuery($queryField, $fieldAst);
                        $next = $gen->current();
                        while ($next) {
                            $received = (yield $next);
                            $childrenScore += (int)$received;
                            $next = $gen->send($received);
                        }
                    }
                }
            }
        }
        // sanity check.  don't yield fragments; they don't contribute to cost
        if ($queryNode instanceof Query || $queryNode instanceof FieldAst) {
            yield [$queryNode, $currentLevelAST, $childrenScore];
        }
    }
}
