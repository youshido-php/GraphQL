<?php

namespace Youshido\GraphQL\Validator\ResolveValidator;

use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

/**
 * Interface ResolveValidatorInterface
 */
interface ResolveValidatorInterface
{
    public function assertValidResolvedValueForField(FieldInterface $field, $resolvedValue);

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface);

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType);
}
