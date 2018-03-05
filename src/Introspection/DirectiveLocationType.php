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
 * Date: 3/24/17.
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Directive\DirectiveLocation;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class DirectiveLocationType extends AbstractEnumType
{
    public const QUERY = DirectiveLocation::QUERY;

    public const MUTATION = DirectiveLocation::MUTATION;

    public const FIELD = DirectiveLocation::FIELD;

    public const FRAGMENT_DEFINITION = DirectiveLocation::FRAGMENT_DEFINITION;

    public const FRAGMENT_SPREAD = DirectiveLocation::FRAGMENT_SPREAD;

    public const INLINE_FRAGMENT = DirectiveLocation::INLINE_FRAGMENT;

    public function getName()
    {
        return '__DirectiveLocation';
    }

    public function getValues()
    {
        return [
            ['name' => 'QUERY', 'value' => self::QUERY],
            ['name' => 'MUTATION', 'value' => self::MUTATION],
            ['name' => 'FIELD', 'value' => self::FIELD],
            ['name' => 'FRAGMENT_DEFINITION', 'value' => self::FRAGMENT_DEFINITION],
            ['name' => 'FRAGMENT_SPREAD', 'value' => self::FRAGMENT_SPREAD],
            ['name' => 'INLINE_FRAGMENT', 'value' => self::INLINE_FRAGMENT],
        ];
    }
}
