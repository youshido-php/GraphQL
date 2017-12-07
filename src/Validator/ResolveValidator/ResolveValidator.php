<?php

namespace Youshido\GraphQL\Validator\ResolveValidator;

use Youshido\GraphQL\Exception\ResolveException;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

/**
 * Class ResolveValidator
 */
class ResolveValidator implements ResolveValidatorInterface
{
    public function assertValidResolvedValueForField(FieldInterface $field, $resolvedValue)
    {
        if (null === $resolvedValue && $field->getType()->getKind() === TypeMap::KIND_NON_NULL) {
            throw new ResolveException(sprintf('Cannot return null for non-nullable field "%s"', $field->getName()));
        }

        $nullableFieldType = $field->getType()->getNullableType();
        if (!$nullableFieldType->isValidValue($resolvedValue)) {
            $error = $nullableFieldType->getValidationError($resolvedValue) ?: '(no details available)';

            throw new ResolveException(sprintf('Not valid resolved type for field "%s": %s', $field->getName(), $error));
        }
    }

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface)
    {
        if ($type instanceof AbstractObjectType) {
            foreach ($type->getInterfaces() as $typeInterface) {
                if ($typeInterface->getName() === $interface->getName()) {
                    return;
                }
            }
        }

        throw new ResolveException(sprintf('Type "%s" does not implement "%s"', $type->getName(), $interface->getName()));
    }

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType)
    {
        foreach ($unionType->getTypes() as $unionTypeItem) {
            if ($unionTypeItem->getName() === $type->getName()) {
                return;
            }
        }

        throw new ResolveException(sprintf('Type "%s" not exist in types of "%s"', $type->getName(), $unionType->getName()));
    }
}
