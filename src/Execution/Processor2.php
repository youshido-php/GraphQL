<?php
/**
 * Date: 03.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Execution;


use Youshido\GraphQL\Execution\Container\Container;
use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\Visitor\MaxComplexityQueryVisitor;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Youshido\GraphQL\Parser\Ast\Mutation as AstMutation;
use Youshido\GraphQL\Parser\Ast\Query as AstQuery;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
use Youshido\GraphQL\Validator\Exception\ResolveException;
use Youshido\GraphQL\Validator\RequestValidator\RequestValidator;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator2;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidatorInterface;

class Processor2
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

    public function __construct(AbstractSchema $schema)
    {
        if (empty($this->executionContext)) {
            $this->executionContext = new ExecutionContext($schema);
            $this->executionContext->setContainer(new Container());
        }

        $this->resolveValidator = new ResolveValidator2($this->executionContext);
    }

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
                    $this->data = array_merge($this->data, $operationResult);
                };
            }
        } catch (\Exception $e) {
            $this->executionContext->addError($e);
        }

        return $this;
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

    protected function resolveQuery(AstQuery $query)
    {
        $schema = $this->executionContext->getSchema();
        $type   = $query instanceof AstMutation ? $schema->getMutationType() : $schema->getQueryType();
        $field  = new Field([
            'name' => $query instanceof AstMutation ? 'mutation' : 'query',
            'type' => $type
        ]);

        $this->resolveValidator->assetTypeHasField($type, $query);
        $value = $this->resolveField($field, $query);

        return [$this->getAlias($query) => $value];
    }

    protected function resolveField(FieldInterface $field, AstFieldInterface $ast, $parentValue = null)
    {
        try {
            /** @var AbstractObjectType $type */
            $type = $field->getType();

            $this->resolveValidator->assetTypeHasField($type, $ast);

            $targetField = $type->getField($ast->getName());

            $this->resolveValidator->assertValidArguments($targetField, $ast);

            switch ($kind = $targetField->getType()->getNullableType()->getKind()) {
                case TypeMap::KIND_ENUM:
                case TypeMap::KIND_SCALAR:
                    if ($ast instanceof AstQuery && $ast->hasFields()) {
                        throw new ResolveException(sprintf('You can\'t specify fields for scalar type "%s"', $type->getName()));
                    }

                    return $this->resolveScalar($targetField, $ast, $parentValue);

                case TypeMap::KIND_OBJECT:
                    /** @var $type AbstractObjectType */
                    if (!$ast instanceof AstQuery) {
                        throw new ResolveException(sprintf('You have to specify fields for "%s"', $ast->getName()));
                    }

                    return $this->resolveObject($targetField, $ast, $parentValue);

                case TypeMap::KIND_LIST:
                    return $this->resolveList($targetField, $ast, $parentValue);

                case TypeMap::KIND_UNION:
                    if (!$ast instanceof AstQuery) {
                        throw new ResolveException(sprintf('You have to specify fields for "%s"', $ast->getName()));
                    }

                    return $this->resolveUnion($targetField, $ast, $parentValue);

                default:
                    throw new ResolveException(sprintf('Resolving type with kind "%s" not supported', $kind));
            }
        } catch (\Exception $e) {
            $this->executionContext->addError($e);

            return null;
        }
    }

    protected function resolveObject(FieldInterface $field, AstFieldInterface $ast, $parentValue, $fromUnion = false)
    {
        if (!$fromUnion) {
            $resolvedValue = $this->doResolve($field, $ast, $parentValue);
        } else {
            $resolvedValue = $parentValue;
        }

        $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

        /** @var AbstractObjectType $type */
        $type = $field->getType()->getNullableType();

        return $this->collectResult($field, $type, $ast, $resolvedValue);
    }

    private function collectResult(FieldInterface $field, AbstractObjectType $type, $ast, $resolvedValue)
    {
        /** @var AstQuery $ast */
        $result = [];

        foreach ($ast->getFields() as $astField) {
            if ($astField instanceof TypedFragmentReference) {
                if ($type->getName() != $astField->getTypeName()) {
                    continue;
                }

                $result = array_merge($result, $this->collectResult($field, $type, $astField, $resolvedValue));
            } elseif ($astField instanceof FragmentReference) {
                //todo
            } else {
                $this->resolveValidator->assetTypeHasField($type, $astField);
                $result[$this->getAlias($astField)] = $this->resolveField($field, $astField, $resolvedValue);
            }
        }

        return $result;
    }

    protected function resolveScalar(FieldInterface $field, AstFieldInterface $ast, $parentValue)
    {
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);

        $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

        /** @var AbstractScalarType $type */
        $type = $field->getType()->getNullableType();

        return $type->serialize($resolvedValue);
    }

    protected function resolveList(FieldInterface $field, AstFieldInterface $ast, $parentValue)
    {
        /** @var AstQuery $ast */
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);

        $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

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
        ]);

        $result = [];
        foreach ($resolvedValue as $resolvedValueItem) {
            try {
                $fakeField->getConfig()->set('resolve', function () use ($resolvedValueItem) {
                    return $resolvedValueItem;
                });

                switch ($itemType->getNullableType()->getKind()) {
                    case TypeMap::KIND_ENUM:
                    case TypeMap::KIND_SCALAR:
                        $value = $this->resolveScalar($fakeField, $fakeAst, $resolvedValueItem);

                        break;


                    case TypeMap::KIND_OBJECT:
                        $value = $this->resolveObject($fakeField, $fakeAst, $resolvedValueItem);

                        break;

                    case TypeMap::KIND_UNION:
                        $value = $this->resolveUnion($fakeField, $fakeAst, $resolvedValueItem);

                        break;

                    default:
                        $value = null;
                }
            } catch (\Exception $e) {
                $this->executionContext->addError($e);

                $value = null;
            }

            $result[] = $value;
        }

        return $result;
    }

    protected function resolveUnion(FieldInterface $field, AstFieldInterface $ast, $parentValue)
    {
        /** @var AstQuery $ast */
        $resolvedValue = $this->doResolve($field, $ast, $parentValue);

        $this->resolveValidator->assertValidResolvedValueForField($field, $resolvedValue);

        /** @var AbstractUnionType $type */
        $type         = $field->getType()->getNullableType();
        $resolvedType = $type->resolveType($resolvedValue);

        if (!$resolvedType) {
            throw new ResolveException('Resoling function must return type');
        }

        if ($type instanceof AbstractInterfaceType) {
            $this->resolveValidator->assertTypeImplementsInterface($resolvedType, $type);
        } else {
            $this->resolveValidator->assertTypeInUnionTypes($resolvedType, $type);
        }

        $fakeField = new Field([
            'name' => $field->getName(),
            'type' => $resolvedType,
        ]);

        return $this->resolveObject($fakeField, $ast, $resolvedValue, true);
    }

    protected function parseAndCreateRequest($payload, $variables = [])
    {
        if (empty($payload)) {
            throw new \InvalidArgumentException('Must provide an operation.');
        }

        $parser  = new Parser();
        $request = new Request($parser->parse($payload), $variables);

        (new RequestValidator())->validate($request);

        $this->executionContext->setRequest($request);
    }

    protected function doResolve(FieldInterface $field, AstFieldInterface $ast, $parentValue = null)
    {
        /** @var AstQuery|AstField $ast */
        $arguments = $this->parseArgumentsValues($field, $ast);
        $astFields = $ast instanceof AstQuery ? $ast->getFields() : [];

        return $field->resolve($parentValue, $arguments, $this->createResolveInfo($field, $astFields));
    }

    protected function parseArgumentsValues(FieldInterface $field, AstFieldInterface $ast)
    {
        return [];//todo
    }

    private function getAlias(AstFieldInterface $ast)
    {
        return $ast->getAlias() ?: $ast->getName();
    }

    private function createResolveInfo(FieldInterface $field, array $astFields)
    {
        return new ResolveInfo($field, $astFields, $this->executionContext);
    }

}
