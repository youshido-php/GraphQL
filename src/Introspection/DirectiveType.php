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
 * Date: 16.12.15.
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Config\Directive\DirectiveConfig;
use Youshido\GraphQL\Directive\Directive;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class DirectiveType extends AbstractObjectType
{
    /**
     * @return string type name
     */
    public function getName()
    {
        return '__Directive';
    }

    public function resolveArgs(DirectiveInterface $value)
    {
        if ($value->hasArguments()) {
            return $value->getArguments();
        }

        return [];
    }

    /**
     * @param Directive|DirectiveInterface $value
     *
     * @return mixed
     */
    public function resolveLocations(DirectiveInterface $value)
    {
        /** @var DirectiveConfig $directiveConfig */
        $directiveConfig = $value->getConfig();

        $locations = $directiveConfig->getLocations();

        return $locations;
    }

    public function build($config): void
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('args', [
                'type'    => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => [$this, 'resolveArgs'],
            ])
            ->addField('locations', [
                'type'    => new NonNullType(new ListType(new NonNullType(new DirectiveLocationType()))),
                'resolve' => [$this, 'resolveLocations'],
            ]);
    }
}
