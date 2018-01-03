<?php

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Directive\IncludeDirective;
use Youshido\GraphQL\Directive\SkipDirective;
use Youshido\GraphQL\Exception\GraphQLException;
use Youshido\GraphQL\Exception\ResolveException;
use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfo;
use Youshido\GraphQL\Execution\Visitor\MaxComplexityQueryVisitor;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Interfaces\DirectivesContainerInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Youshido\GraphQL\Parser\Ast\Mutation as AstMutation;
use Youshido\GraphQL\Parser\Ast\Query as AstQuery;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeKind;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
use Youshido\GraphQL\Validator\RequestValidator\RequestValidator;
use Youshido\GraphQL\Validator\RequestValidator\Validator\FragmentsValidator;
use Youshido\GraphQL\Validator\RequestValidator\Validator\RequestConformityValidator;
use Youshido\GraphQL\Validator\RequestValidator\Validator\VariablesValidator;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidatorInterface;

/**
 * Class Processor
 */
class Processor
{
    const TYPE_NAME_QUERY = '__typename';

    /** @var ExecutionContext */
    protected $executionContext;

    /** @var ResolveValidatorInterface */
    protected $resolveValidator;

    /** @var  array */
    protected $data;

    /** @var int */
    protected $maxComplexity;

    /** @var array DeferredResult[] */
    protected $deferredResults = [];

    /**
     * Processor constructor.
     *
     * @param ExecutionContextInterface $executionContext
     */
    public function __construct(ExecutionContextInterface $executionContext)
    {
        $this->executionContext = $executionContext;
        $this->resolveValidator = new ResolveValidator($this->executionContext);
    }

    /**
     * @param string $payload
     * @param array  $variables
     * @param array  $reducers
     *
     * @return $this
     */
    public function processPayload($payload, $variables = [], $reducers = [])
    {
        $this->data = [];

        try {
            $this->parseAndCreateRequest($payload, $variables);

            if ($this->maxComplexity) {
                $reducers[] = new MaxComplexityQueryVisitor($this->maxComplexity);
            }

            if ($reducers) {
                $reducer = new Reducer();
                $reducer->reduceQuery($this->executionContext, $reducers);
            }

            foreach ($this->executionContext->getRequest()->getAllOperations() as $query) {
                if ($operationResult = $this->resolveQuery($query)) {
                    $this->data = array_replace_recursive($this->data, $operationResult);
                }
            }

            // If the processor found any deferred results, resolve them now.
            if (!empty($this->data) && $this->deferredResults) {
                try {
                    while ($deferredResolver = array_shift($this->deferredResults)) {
                        $deferredResolver->resolve();
                    }
                } catch (\Exception $e) {
                    $this->executionContext->handleException($e);
                } finally {
                    $this->data = $this->unpackDeferredResults($this->data);
                }
            }
        } catch (\Exception $e) {
            $this->executionContext->handleException($e);
        }

        return $this;
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
     * @return array
     */
    public function getResponseData()
    {
        $result = [];

        if (!empty($this->data)) {
            $result['data'] = $this->data;
        }

        if ($this->executionContext->hasErrors()) {
            $result['errors'] = $this->executionContext->getErrorsData();
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getMaxComplexity()
    {
        return $this->maxComplexity;
    }

    /**
     * @param int $maxComplexity
     */
    public function setMaxComplexity($maxComplexity)
    {
        $this->maxComplexity = $maxComplexity;
    }

    /**
     * Unpack results stored inside deferred resolvers.
     *
     * @param mixed $result
     *
     * @return array|mixed
     */
    protected function unpackDeferredResults($result)
    {
        while ($result instanceof DeferredResult) {
            $result = $result->result;
        }

        if (is_array($result)) {
            foreach ($result as $key => $value) {
                $result[$key] = $this->unpackDeferredResults($value);
            }
        }

        return $result;
    }

    protected function resolveQuery(AstQuery $query)
    {
        $schema = $this->executionContext->getSchema();
        $type   = $query instanceof AstMutation ? $schema->getMutationType() : $schema->getQueryType();
        $field  = new Field(['name' => $type->getName(), 'type' => $type]);

        if (self::TYPE_NAME_QUERY === $query->getName()) {
            return [$this->getAlias($query) => $type->getName()];
        }

        if ($this->skipCollecting($query)) {
            return [];
        }

        return [$this->getAlias($query) => $this->resolveField($field, $query)];
    }

    protected function resolveField(FieldInterface $field, AstFieldInterface $ast, $parentValue = null, $fromObject = false)
    {
        try {
            /** @var AbstractObjectType $type */
            $type        = $field->getType();
            $nonNullType = $type->getNullableType();

            if (self::TYPE_NAME_QUERY === $ast->getName()) {
                return $nonNullType->getName();
            }

            $targetField = $nonNullType->getField($ast->getName());

            switch ($kind = $targetField->getType()->getNullableType()->getKind()) {
                case TypeKind::KIND_ENUM:
                case TypeKind::KIND_SCALAR:
                    if ($ast instanceof AstQuery && $ast->hasFields()) {
                        throw new ResolveException(sprintf('You can\'t specify fields for scalar type "%s"', $targetField->getType()->getNullableType()->getName()), $ast->getLocation());
                    }

                    return $this->resolveScalar($targetField, $ast, $parentValue);

                case TypeKind::KIND_OBJECT:
                    /** @var $type AbstractObjectType */
                    if (!$ast instanceof AstQuery) {
                        throw new ResolveException(sprintf('You have to specify fields for "%s"', $ast->getName()), $ast->getLocation());
                    }

                    return $this->resolveObject($targetField, $ast, $parentValue);

                case TypeKind::KIND_LIST:
                    return $this->resolveList($targetField, $ast, $parentValue);

                case TypeKind::KIND_UNION:
                case TypeKind::KIND_INTERFACE:
                    if (!$ast instanceof AstQuery) {
                        throw new ResolveException(sprintf('You have to specify fields for "%s"', $ast->getName()), $ast->getLocation());
                    }

                    return $this->resolveComposite($targetField, $ast, $parentValue);

                default:
                    throw new ResolveException(sprintf('Resolving type with kind "%s" not supported', $kind));
            }
        } catch (\Exception $e) {
            $this->executionContext->handleException($e);

            if ($fromObject) {
                throw $e;
            }

            return null;
        }
    }

    private function skipCollecting(DirectivesContainerInterface $ast)
    {
        return SkipDirective::check($ast) || IncludeDirective::check($ast);
    }

    /**
     * @param FieldInterface     $field
     * @param AbstractObjectType $type
     * @param AstFieldInterface  $ast
     * @param                    $resolvedValue
     *
     * @return array
     */
    private function collectResult(FieldInterface $field, AbstractObjectType $type, $ast, $resolvedValue)
    {
        $result = [];

        foreach ($ast->getFields() as $astField) {
            if ($this->skipCollecting($astField)) {
                continue;
            }

            switch (true) {
                case $astField instanceof TypedFragmentReference:
                    $astName  = $astField->getTypeName();
                    $typeName = $type->getName();

                    if ($typeName !== $astName) {
                        foreach ($type->getInterfaces() as $interface) {
                            if ($interface->getName() === $astName) {
                                $result = array_replace_recursive($result, $this->collectResult($field, $type, $astField, $resolvedValue));

                                break;
                            }
                        }

                        continue 2;
                    }

                    $result = array_replace_recursive($result, $this->collectResult($field, $type, $astField, $resolvedValue));

                    break;

                case $astField instanceof FragmentReference:
                    $astFragment      = $this->executionContext->getRequest()->getFragment($astField->getName());
                    $astFragmentModel = $astFragment->getModel();
                    $typeName         = $type->getName();

                    if ($typeName !== $astFragmentModel && $type->getInterfaces()) {
                        foreach ($type->getInterfaces() as $interface) {
                            if ($interface->getName() === $astFragmentModel) {
                                $result = array_replace_recursive($result, $this->collectResult($field, $type, $astFragment, $resolvedValue));

                                break;
                            }
                        }

                        continue 2;
                    }

                    $result = array_replace_recursive($result, $this->collectResult($field, $type, $astFragment, $resolvedValue));

                    break;

                default:
                    $result = array_replace_recursive($result, [$this->getAlias($astField) => $this->resolveField($field, $astField, $resolvedValue, true)]);
            }
        }

        return $result;
    }

    /**
     * Apply post-process callbacks to all deferred resolvers.
     *
     * @param mixed    $resolvedValue
     * @param callable $callback
     *
     * @return DeferredResult
     */
    protected function deferredResolve($resolvedValue, callable $callback)
    {
        if ($resolvedValue instanceof DeferredResolverInterface) {
            $deferredResult = new DeferredResult($resolvedValue, function ($resolvedValue) use ($callback) {
                return $this->deferredResolve($resolvedValue, $callback);
            });
            // Whenever we stumble upon a deferred resolver, append it to the
            // queue to be resolved later.
            $this->deferredResults[] = $deferredResult;

            return $deferredResult;
        }

        // For simple values, invoke the callback immediately.
        return $callback($resolvedValue);
    }

    protected function resolveScalar(FieldInterface $field, AstFieldInterface $ast, $parentValue)
    {
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);

        return $this->deferredResolve($resolvedValue, function ($resolvedValue) use ($field, $ast, $parentValue) {
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

            /** @var AbstractScalarType $type */
            $type = $field->getType()->getNullableType();

            return $type->serialize($resolvedValue);
        });
    }

    protected function resolveList(FieldInterface $field, AstFieldInterface $ast, $parentValue)
    {
        /** @var AstQuery $ast */
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);

        return $this->deferredResolve($resolvedValue, function ($resolvedValue) use ($field, $ast) {
            /** @var array $resolvedValue */
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

            if (null === $resolvedValue) {
                return null;
            }

            /** @var AbstractListType $type */
            $type     = $field->getType()->getNullableType();
            $itemType = $type->getNamedType();

            $fakeAst = clone $ast;
            if ($fakeAst instanceof AstQuery) {
                $fakeAst->setArguments([]);
            }

            $fakeField = new Field([
                'name' => $field->getName(),
                'type' => $itemType,
                'args' => $field->getArguments(),
            ]);

            $result = [];
            foreach ($resolvedValue as $resolvedValueItem) {
                try {
                    $fakeField->getConfig()->set('resolve', function () use ($resolvedValueItem) {
                        return $resolvedValueItem;
                    });

                    switch ($itemType->getNullableType()->getKind()) {
                        case TypeKind::KIND_ENUM:
                        case TypeKind::KIND_SCALAR:
                            $value = $this->resolveScalar($fakeField, $fakeAst, $resolvedValueItem);

                            break;


                        case TypeKind::KIND_OBJECT:
                            $value = $this->resolveObject($fakeField, $fakeAst, $resolvedValueItem);

                            break;

                        case TypeKind::KIND_UNION:
                        case TypeKind::KIND_INTERFACE:
                            $value = $this->resolveComposite($fakeField, $fakeAst, $resolvedValueItem);

                            break;

                        default:
                            $value = null;
                    }
                } catch (\Exception $e) {
                    $this->executionContext->handleException($e);

                    $value = null;
                }

                $result[] = $value;
            }

            return $result;
        });
    }

    protected function resolveObject(FieldInterface $field, AstFieldInterface $ast, $parentValue, $fromUnion = false)
    {
        $resolvedValue = $parentValue;
        if (!$fromUnion) {
            $resolvedValue = $this->doResolve($field, $ast, $parentValue);
        }

        return $this->deferredResolve($resolvedValue, function ($resolvedValue) use ($field, $ast) {
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

            if (null === $resolvedValue) {
                return null;
            }

            /** @var AbstractObjectType $type */
            $type = $field->getType()->getNullableType();

            try {
                return $this->collectResult($field, $type, $ast, $resolvedValue);
            } catch (\Exception $e) {
                $this->executionContext->handleException($e);

                return null;
            }
        });
    }

    protected function resolveComposite(FieldInterface $field, AstFieldInterface $ast, $parentValue)
    {
        /** @var AstQuery $ast */
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);

        return $this->deferredResolve($resolvedValue, function ($resolvedValue) use ($field, $ast) {
            $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

            if (null === $resolvedValue) {
                return null;
            }

            /** @var AbstractUnionType $type */
            $type         = $field->getType()->getNullableType();
            $resolvedType = $type->resolveType($resolvedValue, $this->createResolveInfo($field, $ast instanceof AstQuery ? $ast->getFields() : []));

            if (!$resolvedType) {
                throw new ResolveException('Resolving function must return type');
            }

            if ($type instanceof AbstractInterfaceType) {
                $this->resolveValidator->assertTypeImplementsInterface($resolvedType, $type);
            } else {
                $this->resolveValidator->assertTypeInUnionTypes($resolvedType, $type);
            }

            $fakeField = new Field([
                'name' => $field->getName(),
                'type' => $resolvedType,
                'args' => $field->getArguments(),
            ]);

            return $this->resolveObject($fakeField, $ast, $resolvedValue, true);
        });
    }

    protected function parseAndCreateRequest($payload, $variables)
    {
        if (empty($payload)) {
            throw new GraphQLException('Must provide an operation.');
        }

        $parser  = new Parser();
        $request = new Request($parser->parse($payload), $variables);

        $requestValidator = new RequestValidator([
            new FragmentsValidator(),
            new VariablesValidator(),
            new RequestConformityValidator(),
        ]);

        $requestValidator->validate($request, $this->executionContext->getSchema());
        $this->executionContext->setRequest($request);
    }

    protected function doResolve(FieldInterface $field, AstFieldInterface $ast, $parentValue = null)
    {
        /** @var AstQuery|AstField $ast */
        $arguments = $this->parseArgumentsValues($field, $ast);
        $astFields = $ast instanceof AstQuery ? $ast->getFields() : [];

        return $field->resolve($parentValue, $arguments, $this->createResolveInfo($field, $astFields));
    }

    protected function createResolveInfo(FieldInterface $field, array $astFields)
    {
        return new ResolveInfo($field, $astFields, $this->executionContext);
    }

    protected function parseArgumentsValues(FieldInterface $field, AstFieldInterface $ast)
    {
        $values   = [];
        $defaults = [];

        foreach ($field->getArguments() as $argument) {
            /** @var $argument InputField */
            if ($argument->getConfig()->has('defaultValue')) {
                $defaults[$argument->getName()] = $argument->getConfig()->getDefaultValue();
            }
        }

        foreach ($ast->getArguments() as $astArgument) {
            $argument     = $field->getArgument($astArgument->getName());
            $argumentType = $argument->getType()->getNullableType();

            $values[$argument->getName()] = $argumentType->parseValue($astArgument->getValue());

            if (isset($defaults[$argument->getName()])) {
                unset($defaults[$argument->getName()]);
            }
        }

        return array_merge($values, $defaults);
    }

    private function getAlias(AstFieldInterface $ast)
    {
        return $ast->getAlias() ?: $ast->getName();
    }
}
