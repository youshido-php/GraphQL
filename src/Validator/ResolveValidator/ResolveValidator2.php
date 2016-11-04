<?php
/**
 * Date: 03.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;


use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Parser\Ast\Argument as AstArgument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
use Youshido\GraphQL\Validator\Exception\ResolveException;

class ResolveValidator2
{

    public function assetTypeHasField(AbstractType $objectType, AstFieldInterface $ast)
    {
        /** @var AbstractObjectType $objectType */
        if (!(TypeService::isObjectType($objectType) || TypeService::isInputObjectType($objectType)) || !$objectType->hasField($ast->getName())) {
            throw new ResolveException(sprintf('Field "%s" not found in type "%s"', $ast->getName(), $objectType->getNamedType()->getName()));
        }
    }

    public function assertValidArguments(FieldInterface $field, AstFieldInterface $query)
    {
        $requiredArguments = array_filter($field->getArguments(), function (InputField $argument) {
            return $argument->getType()->getKind() == TypeMap::KIND_NON_NULL;
        });

        foreach ($query->getArguments() as $astArgument) {
            if (!$field->hasArgument($astArgument->getName())) {
                throw new ResolveException(sprintf('Unknown argument "%s" on field "%s"', $astArgument->getName(), $field->getName()));
            }

            $argument     = $argumentType = $field->getArgument($astArgument->getName());
            $argumentType = $argument->getType()->getNullableType();

            switch ($argumentType->getKind()) {
                case TypeMap::KIND_ENUM:
                case TypeMap::KIND_SCALAR:
                case TypeMap::KIND_INPUT_OBJECT:
                case TypeMap::KIND_LIST:
                    if (!$argumentType->isValidValue($this->getArgumentValue($astArgument))) {
                        throw new ResolveException(sprintf('Not valid type for argument "%s" in query "%s"', $astArgument->getName(), $field->getName()));
                    }

                    break;

                default:
                    throw new ResolveException(sprintf('Invalid argument type "%s"', $argumentType->getName()));

            }

            if (array_key_exists($astArgument->getName(), $requiredArguments) || $argument->getConfig()->get('default') !== null) {
                unset($requiredArguments[$astArgument->getName()]);
            }
        }

        if (count($requiredArguments)) {
            throw new ResolveException(sprintf('Require "%s" arguments to query "%s"', implode(', ', array_keys($requiredArguments)), $query->getName()));
        }
    }

    private function getArgumentValue(AstArgument $argument)
    {
        if ($argument->getValue() instanceof VariableReference) {
            //todo

            return null;
        }

        return $argument->getValue()->getValue();
    }

    public function assertValidResolvedValueForField(FieldInterface $field, $resolvedValue)
    {
        if ($field->getType()->getKind() == TypeMap::KIND_NON_NULL && is_null($resolvedValue)) {
            throw new ResolveException(sprintf('Cannot return null for non-nullable field "%s"', $field->getName()));
        }

        if (!$field->getType()->getNullableType()->isValidValue($resolvedValue)) {
            throw new ResolveException(sprintf('Not valid resolved type for field "%s"', $field->getName()));
        }
    }

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface)
    {
        if (!$interface->isValidValue($type)) {
            throw new ResolveException('Type ' . $type->getName() . ' does not implement ' . $interface->getName());
        }
    }

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType)
    {
        foreach ($unionType->getTypes() as $unionTypeItem) {
            if ($unionTypeItem->getName() == $type->getName()) {
                return;
            }
        }

        throw new ResolveException('Type ' . $type->getName() . ' not exist in types of ' . $unionType->getName());
    }

}
