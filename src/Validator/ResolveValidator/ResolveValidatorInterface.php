<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 01.12.15.
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;

use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface as AstFieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

interface ResolveValidatorInterface
{
    public function assetTypeHasField(AbstractType $objectType, AstFieldInterface $ast);

    public function assertValidArguments(FieldInterface $field, AstFieldInterface $query, Request $request);

    public function assertValidResolvedValueForField(FieldInterface $field, $resolvedValue);

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface);

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType);
}
