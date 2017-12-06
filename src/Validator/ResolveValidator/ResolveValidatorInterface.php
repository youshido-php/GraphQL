<?php

namespace Youshido\GraphQL\Validator\ResolveValidator;

use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\ArgumentsContainerInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\ArgumentsContainerInterface as AstArgumentsContainerInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

/**
 * Interface ResolveValidatorInterface
 */
interface ResolveValidatorInterface
{
    public function assetTypeHasField(AbstractType $objectType, AstFieldInterface $ast);

    public function assertValidArguments(ArgumentsContainerInterface $field, AstArgumentsContainerInterface $query, Request $request);

    public function assertValidResolvedValueForField(FieldInterface $field, $resolvedValue);

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface);

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType);
}
