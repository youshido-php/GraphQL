<?php

namespace Youshido\GraphQL\Validator\RequestValidator\Validator;

use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Exception\ResolveException;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Execution\TypeCollector;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\AbstractInputField;
use Youshido\GraphQL\Field\ArgumentsContainerInterface;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputList as AstInputList;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputObject as AstInputObject;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal as AstLiteral;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\Fragment as AstFragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Interfaces\ArgumentsContainerInterface as AstArgumentsContainerInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\NamedFieldInterface;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query as AstQuery;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference as AstTypedFragmentReference;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
use Youshido\GraphQL\Validator\RequestValidator\RequestValidatorInterface;

/**
 * Class RequestConformityValidator
 */
class RequestConformityValidator implements RequestValidatorInterface
{
    /** @var  AbstractSchema */
    private $schema;

    /** @var  Request */
    private $request;

    /**
     * @param Request        $request
     * @param AbstractSchema $schema
     *
     * @throws ResolveException
     */
    public function validate(Request $request, AbstractSchema $schema)
    {
        $this->request = $request;
        $this->schema  = $schema;

        foreach ($request->getAllOperations() as $operation) {
            $type = $operation instanceof Mutation ? $schema->getMutationType() : $schema->getQueryType();

            $this->assertAstConformity($type, $operation);
        }
    }

    /**
     * @param AbstractType                  $type
     * @param AbstractAst|AstFieldInterface $ast
     * @param bool                          $checkSubField
     *
     * @throws ResolveException
     */
    private function assertAstConformity(AbstractType $type, AbstractAst $ast, $checkSubField = true)
    {
        $targetType = $type->getNullableType();

        if ($checkSubField) {
            $this->assetTypeHasField($type, $ast);

            /** @var AbstractField $targetField */
            $targetField = $type->getNullableType()->getField($ast->getName());
            $targetType  = $targetField->getType()->getNullableType();

            $this->assertValidArguments($targetField, $ast, $this->request);
        }

        if ($ast->hasDirective('skip') || $ast->hasDirective('include')) {
            $directiveAst = $ast->hasDirective('skip') ? $ast->getDirective('skip') : $ast->getDirective('include');

            $directive = $this->schema->getDirectives()->get($directiveAst->getName());
            $this->prepareAstArguments($directive, $directiveAst, $this->request);
            $this->assertValidArguments($directive, $directiveAst, $this->request);
        }

        switch ($kind = $targetType->getKind()) {
            case TypeMap::KIND_ENUM:
            case TypeMap::KIND_SCALAR:
                if ($ast instanceof AstQuery && $ast->hasFields()) {
                    throw new ResolveException(sprintf('You can\'t specify fields for scalar type "%s"', $targetType->getName()), $ast->getLocation());
                }

                break;

            case TypeMap::KIND_OBJECT:
                /** @var $type AbstractObjectType */
                if ((!$ast instanceof AstQuery && !$ast instanceof AstFragment) || !$ast->getFields()) {
                    throw new ResolveException(sprintf('You have to specify fields for "%s"', $ast->getName()), $ast->getLocation());
                }

                foreach ($ast->getFields() as $field) {
                    if ($field instanceof FragmentReference) {
                        $field = $this->request->getFragment($field->getName());
                    }

                    $this->assertAstConformity($targetType, $field, !$field instanceof AstFragment);
                }

                break;

            case TypeMap::KIND_LIST:
                $type = $targetType->getNamedType()->getNullableType();

                if (!$ast instanceof AstQuery && $type->getKind() === TypeMap::KIND_OBJECT) {
                    throw new ResolveException(sprintf('You have to specify fields for "%s"', $ast->getName()), $ast->getLocation());
                }

                if (!$ast instanceof AstField && in_array($type->getKind(), [TypeMap::KIND_ENUM, TypeMap::KIND_SCALAR], false)) {
                    throw new ResolveException('You can\'t specify fields for scalars or enums', $ast->getLocation());
                }

                if ($ast instanceof AstQuery) {
                    $this->assertAstConformity($type, $ast, false);
                }

                break;

            case TypeMap::KIND_UNION:
            case TypeMap::KIND_INTERFACE:
                if (!$ast instanceof AstQuery) {
                    throw new ResolveException(sprintf('You have to specify fields for "%s"', $ast->getName()), $ast->getLocation());
                }

                foreach ($ast->getFields() as $field) {
                    if ($field instanceof AstTypedFragmentReference || $field instanceof FragmentReference) {
                        $fragment       = $field instanceof FragmentReference ? $this->request->getFragment($field->getName()) : $field;
                        $isTypePossible = false;
                        $subType        = null;
                        $typeName       = $field instanceof FragmentReference ? $fragment->getModel() : $field->getTypeName();


                        /** @var AbstractUnionType|AbstractInterfaceType $targetType */
                        if ($targetType->getKind() === TypeMap::KIND_UNION) {
                            $possibleTypes = $targetType->getTypes();
                        } else {
                            $collector = TypeCollector::getInstance();
                            $collector->addSchema($this->schema);
                            $possibleTypes = $collector->getInterfacePossibleTypes($targetType->getName());
                        }

                        foreach ($possibleTypes as $possibleType) {
                            if ($possibleType->getNullableType()->getName() === $typeName) {
                                $isTypePossible = true;
                                $subType        = $possibleType;

                                break;
                            }
                        }

                        if ($isTypePossible) {
                            foreach ($fragment->getFields() as $subField) {
                                $this->assertAstConformity($subType, $subField);
                            }

                            continue;
                        }

                        throw new ResolveException(sprintf('Type "%s" not found in possible types of composite type "%s"', $typeName, $targetType->getName()), $field->getLocation());
                    }

                    if ($field instanceof AstField && $field->getName() === '__typename') {
                        continue;
                    }

                    if ($type->getKind() === TypeMap::KIND_UNION) {
                        throw new ResolveException(sprintf('You must use typed fragment to query fields on union type "%s"', $targetType->getName()), $field->getLocation());
                    }

                    $this->assertAstConformity($targetType, $field);
                }

                break;

            default:
                throw new ResolveException(sprintf('Resolving type with kind "%s" not supported', $kind));
        }
    }

    private function prepareAstArguments(ArgumentsContainerInterface $field, AstArgumentsContainerInterface $query, Request $request)
    {
        foreach ($query->getArguments() as $astArgument) {
            if ($field->hasArgument($astArgument->getName())) {
                $argumentType = $field->getArgument($astArgument->getName())->getType()->getNullableType();

                $astArgument->setValue($this->prepareArgumentValue($astArgument->getValue(), $argumentType, $request));
            }
        }
    }

    private function assertValidArguments(ArgumentsContainerInterface $field, AstArgumentsContainerInterface $query, Request $request)
    {
        /** @var $field ArgumentsContainerInterface|DirectiveInterface|FieldInterface */
        /** @var $query AstArgumentsContainerInterface|AstQuery */
        $this->prepareAstArguments($field, $query, $request);

        $requiredArguments = array_filter($field->getArguments(), function (AbstractInputField $argument) {
            return $argument->getType()->getKind() === TypeMap::KIND_NON_NULL;
        });

        foreach ($query->getArguments() as $astArgument) {
            if (!$field->hasArgument($astArgument->getName())) {
                throw new ResolveException(sprintf('Unknown argument "%s" on field "%s"', $astArgument->getName(), $field->getName()), $astArgument->getLocation());
            }

            $argument     = $field->getArgument($astArgument->getName());
            $argumentType = $argument->getType()->getNullableType();

            switch ($argumentType->getKind()) {
                case TypeMap::KIND_ENUM:
                case TypeMap::KIND_SCALAR:
                case TypeMap::KIND_INPUT_OBJECT:
                case TypeMap::KIND_LIST:
                    if (!$argument->getType()->isValidValue($astArgument->getValue())) {
                        $error = $argument->getType()->getValidationError($astArgument->getValue()) ?: '(no details available)';
                        throw new ResolveException(sprintf('Not valid type for argument "%s" in query "%s": %s', $astArgument->getName(), $field->getName(), $error), $astArgument->getLocation());
                    }

                    break;

                default:
                    throw new ResolveException(sprintf('Invalid argument type "%s"', $argumentType->getName()));
            }

            if (array_key_exists($astArgument->getName(), $requiredArguments) || $argument->getConfig()->get('defaultValue') !== null) {
                unset($requiredArguments[$astArgument->getName()]);
            }
        }

        if (count($requiredArguments)) {
            throw new ResolveException(sprintf('Require "%s" arguments to query "%s"', implode(', ', array_keys($requiredArguments)), $query->getName()));
        }
    }

    private function prepareArgumentValue($argumentValue, AbstractType $argumentType, Request $request)
    {
        switch ($argumentType->getKind()) {
            case TypeMap::KIND_LIST:
                /** @var $argumentType AbstractListType */
                $result = [];
                if ($argumentValue instanceof AstInputList || is_array($argumentValue)) {
                    $list = is_array($argumentValue) ? $argumentValue : $argumentValue->getValue();
                    foreach ($list as $item) {
                        $result[] = $this->prepareArgumentValue($item, $argumentType->getItemType()->getNullableType(), $request);
                    }
                } else {
                    if ($argumentValue instanceof VariableReference) {
                        return $this->getVariableReferenceArgumentValue($argumentValue, $argumentType, $request);
                    }
                }

                return $result;

            case TypeMap::KIND_INPUT_OBJECT:
                /** @var $argumentType AbstractInputObjectType */
                $result = [];
                if ($argumentValue instanceof AstInputObject) {
                    foreach ($argumentType->getFields() as $field) {
                        /** @var $field AbstractField */
                        if ($field->getConfig()->has('defaultValue')) {
                            $result[$field->getName()] = $field->getType()->getNullableType()->parseInputValue($field->getConfig()->get('defaultValue'));
                        }
                    }
                    foreach ($argumentValue->getValue() as $key => $item) {
                        if ($argumentType->hasField($key)) {
                            $result[$key] = $this->prepareArgumentValue($item, $argumentType->getField($key)->getType()->getNullableType(), $request);
                        } else {
                            $result[$key] = $item;
                        }
                    }
                } else {
                    if ($argumentValue instanceof VariableReference) {
                        return $this->getVariableReferenceArgumentValue($argumentValue, $argumentType, $request);
                    }

                    if (is_array($argumentValue)) {
                        return $argumentValue;
                    }
                }

                return $result;

            case TypeMap::KIND_SCALAR:
            case TypeMap::KIND_ENUM:
                /** @var $argumentValue AstLiteral|VariableReference */
                if ($argumentValue instanceof VariableReference) {
                    return $this->getVariableReferenceArgumentValue($argumentValue, $argumentType, $request);
                }
                if ($argumentValue instanceof AstLiteral) {
                    return $argumentValue->getValue();
                }

                return $argumentValue;
        }

        throw new ResolveException('Argument type not supported');
    }

    private function getVariableReferenceArgumentValue(VariableReference $variableReference, AbstractType $argumentType, Request $request)
    {
        $variable = $variableReference->getVariable();
        if ($argumentType->getKind() === TypeMap::KIND_LIST) {
            if (
                (!$variable->isArray() && !is_array($variable->getValue())) ||
                ($argumentType->getNamedType()->getKind() === TypeMap::KIND_NON_NULL && $variable->isArrayElementNullable()) ||
                ($variable->getTypeName() !== $argumentType->getNamedType()->getNullableType()->getName())
            ) {
                throw new ResolveException(sprintf('Invalid variable "%s" type, allowed type is "%s"', $variable->getName(), $argumentType->getNamedType()->getNullableType()->getName()), $variable->getLocation());
            }
        } else {
            if ($variable->getTypeName() !== $argumentType->getName()) {
                throw new ResolveException(sprintf('Invalid variable "%s" type, allowed type is "%s"', $variable->getName(), $argumentType->getName()), $variable->getLocation());
            }
        }

        $requestValue = $request->getVariable($variable->getName());
        if ((null === $requestValue && $variable->isNullable()) && !$request->hasVariable($variable->getName())) {
            throw new ResolveException(sprintf('Variable "%s" does not exist in request', $variable->getName()), $variable->getLocation());
        }

        return $requestValue;
    }

    /**
     * @param AbstractType|AbstractObjectType $objectType
     * @param NamedFieldInterface             $ast
     *
     * @throws ResolveException
     */
    private function assetTypeHasField(AbstractType $objectType, NamedFieldInterface $ast)
    {
        if (!in_array($objectType->getKind(), [TypeMap::KIND_OBJECT, TypeMap::KIND_INPUT_OBJECT, TypeMap::KIND_INTERFACE], false) || !$objectType->hasField($ast->getName())) {
            $availableFieldNames = implode(', ', array_map(function (FieldInterface $field) {
                return sprintf('"%s"', $field->getName());
            }, $objectType->getFields()));

            throw new ResolveException(sprintf('Field "%s" not found in type "%s". Available fields are: %s', $ast->getName(), $objectType->getNamedType()->getName(), $availableFieldNames), $ast->getLocation());
        }
    }
}
