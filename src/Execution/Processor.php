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
use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Execution\Visitor\AbstractQueryVisitor;
use Youshido\GraphQL\Execution\Visitor\MaxComplexityQueryVisitor;
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

    public function __construct(AbstractSchema $schema, ExecutionContextInterface $executionContent = null)
    {
        if (!$executionContent) {
            $executionContent = new ExecutionContext();
        }
        $this->executionContext = $executionContent;

        try {
            (new SchemaValidator())->validate($schema);
        } catch (\Exception $e) {
            $this->executionContext->addError($e);
        };

        $this->introduceIntrospectionFields($schema);
        $this->executionContext->setSchema($schema);

        $this->resolveValidator = new ResolveValidator($this->executionContext);
    }

    public function processPayload($payload, $variables = [], $reducers = [])
    {

        $this->data = [];

        try {
            $this->parseAndCreateRequest($payload, $variables);

            $queryType    = $this->executionContext->getSchema()->getQueryType();
            $mutationType = $this->executionContext->getSchema()->getMutationType();

            if ($this->maxComplexity) {
                $reducers[] = new MaxComplexityQueryVisitor($this->maxComplexity);
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

        $resolvedValue = $this->resolveFieldValue($field, $contextValue, $query->getFields(), $this->parseArgumentsValues($field, $query));

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
     * @throws ResolveException
     */
    protected function collectValueForQueryWithType(Query $query, AbstractType $fieldType, $resolvedValue)
    {
        if (is_null($resolvedValue)) {
            return null;
        }

        $value = [];

        if (!$query->hasFields()) {
            $fieldType = $this->resolveValidator->resolveTypeIfAbstract($fieldType, $resolvedValue);

            if (TypeService::isObjectType($fieldType->getNamedType())) {
                throw new ResolveException(sprintf('You have to specify fields for "%s"', $query->getName()));
            }
            if (TypeService::isScalarType($fieldType)) {
                return $this->getOutputValue($fieldType, $resolvedValue);
            }
        }

        if ($fieldType->getKind() == TypeMap::KIND_LIST) {
            if (!$this->resolveValidator->hasArrayAccess($resolvedValue)) return null;

            $namedType          = $fieldType->getNamedType();
            $isAbstract         = TypeService::isAbstractType($namedType);
            $validItemStructure = false;

            foreach ($resolvedValue as $resolvedValueItem) {
                $value[] = [];
                $index   = count($value) - 1;

                if ($isAbstract) {
                    $namedType = $this->resolveValidator->resolveAbstractType($fieldType->getNamedType(), $resolvedValueItem);
                }

                if (!$validItemStructure) {
                    if (!$namedType->isValidValue($resolvedValueItem)) {
                        $this->executionContext->addError(new ResolveException(sprintf('Not valid resolve value in %s field', $query->getName())));
                        $value[$index] = null;
                        continue;
                    }
                    $validItemStructure = true;
                }

                $value[$index] = $this->processQueryFields($query, $namedType, $resolvedValueItem, $value[$index]);
            }
        } else {
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
            $type      = $fieldType->getNamedType();

            foreach ($preResolvedValue as $resolvedValueItem) {
                $listValue[] = $this->getOutputValue($type, $resolvedValueItem);
            }

            $value = $listValue;
        } else {
            $value = $this->getOutputValue($fieldType, $preResolvedValue);
        }

        return $value;
    }

    protected function createResolveInfo($field, $fields)
    {
        $resolveInfo = new ResolveInfo($field, $fields, $this->executionContext);
        $resolveInfo->setContainer($this->getExecutionContext()->getContainer());
        return $resolveInfo;
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
        if ($field->hasArguments() && !$this->resolveValidator->validateArguments($field, $fieldAst, $this->executionContext->getRequest())) {
            throw new \Exception(sprintf('Not valid arguments for the field "%s"', $fieldAst->getName()));

        }

        return $this->resolveFieldValue($field, $contextValue, [$fieldAst], $fieldAst->getKeyValueArguments());

    }

    protected function resolveFieldValue(AbstractField $field, $contextValue, array $fields, array $args)
    {
        return $field->resolve($contextValue, $args, $this->createResolveInfo($field, $fields));
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
        $originalType = $queryType;
        $queryType    = $this->resolveValidator->resolveTypeIfAbstract($queryType, $resolvedValue);
        $currentType  = $queryType->getNullableType();


        if ($currentType->getKind() == TypeMap::KIND_SCALAR) {
            if (!$query->hasFields()) {
                return $this->getOutputValue($currentType, $resolvedValue);
            } else {
                $this->executionContext->addError(new ResolveException(sprintf('Fields are not found in query "%s"', $query->getName())));

                return null;
            }
        }

        foreach ($query->getFields() as $fieldAst) {

            if ($fieldAst instanceof FragmentInterface) {
                /** @var TypedFragmentReference $fragment */
                $fragment = $fieldAst;
                if ($fieldAst instanceof FragmentReference) {
                    /** @var Fragment $fragment */
                    $fieldAstName = $fieldAst->getName();
                    $fragment     = $this->executionContext->getRequest()->getFragment($fieldAstName);
                    $this->resolveValidator->assertValidFragmentForField($fragment, $fieldAst, $originalType);
                } elseif ($fragment->getTypeName() !== $queryType->getName()) {
                    continue;
                }

                $fragmentValue = $this->processQueryFields($fragment, $queryType, $resolvedValue, $value);
                $value         = is_array($fragmentValue) ? $fragmentValue : [];
            } else {
                $fieldAstName = $fieldAst->getName();
                $alias        = $fieldAst->getAlias() ?: $fieldAstName;

                if ($fieldAstName == self::TYPE_NAME_QUERY) {
                    $value[$alias] = $queryType->getName();
                } else {
                    $field = $currentType->getField($fieldAstName);
                    if (!$field) {
                        $this->executionContext->addError(new ResolveException(sprintf('Field "%s" is not found in type "%s"', $fieldAstName, $currentType->getName())));

                        return null;
                    }
                    if ($fieldAst instanceof Query) {
                        $value[$alias] = $this->processQueryAST($fieldAst, $field, $resolvedValue);
                    } elseif ($fieldAst instanceof FieldAst) {
                        $value[$alias] = $this->processFieldAST($fieldAst, $field, $resolvedValue);
                    } else {
                        return $value;
                    }
                }
            }

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

    protected function introduceIntrospectionFields(AbstractSchema $schema)
    {
        $schemaField = new SchemaField();
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

    /**
     * You can access ExecutionContext to check errors and inject dependencies
     *
     * @return ExecutionContext
     */
    public function getExecutionContext()
    {
        return $this->executionContext;
    }

    /**
     * Convenience function for attaching a MaxComplexityQueryVisitor($max) to the next processor run
     *
     * @param int $max
     */
    public function setMaxComplexity($max)
    {
        $this->maxComplexity = $max;
    }

    /**
     * Apply all of $reducers to this query.  Example reducer operations: checking for maximum query complexity,
     * performing look-ahead query planning, etc.
     *
     * @param AbstractType           $queryType
     * @param AbstractType           $mutationType
     * @param AbstractQueryVisitor[] $reducers
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
     * Entry point for the `walkQuery` routine.  Execution bounces between here, where the reducer's ->visit() method
     * is invoked, and `walkQuery` where we send in the scores from the `visit` call.
     *
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
     * Coroutine to walk the query and schema in DFS manner (see AbstractQueryVisitor docs for more info) and yield a
     * tuple of (queryNode, schemaNode, childScore)
     *
     * childScore costs are accumulated via values sent into the coroutine.
     *
     * Most of the branching in this function is just to handle the different types in a query: Queries, Unions,
     * Fragments (anonymous and named), and Fields.  The core of the function is simple: recurse until we hit the base
     * case of a Field and yield that back up to the visitor up in `doVisit`.
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
            // BASE CASE.  If we're here we're done recursing -
            // this node is either a field, or a query that we've finished recursing into.
            yield [$queryNode, $currentLevelAST, $childrenScore];
        }
    }
}
