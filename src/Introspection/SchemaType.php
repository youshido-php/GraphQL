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
 * Date: 03.12.15.
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Field\TypesField;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class SchemaType extends AbstractObjectType
{
    /**
     * @return string type name
     */
    public function getName()
    {
        return '__Schema';
    }

    public function resolveQueryType($value)
    {
        /* @var AbstractSchema|Field $value */
        return $value->getQueryType();
    }

    public function resolveMutationType($value)
    {
        /* @var AbstractSchema|Field $value */
        return $value->getMutationType()->hasFields() ? $value->getMutationType() : null;
    }

    public function resolveSubscriptionType()
    {
    }

    public function resolveDirectives($value)
    {
        /** @var AbstractSchema|Field $value */
        $dirs = $value->getDirectiveList()->getDirectives();

        return $dirs;
    }

    public function build($config): void
    {
        $config
            ->addField(new Field([
                'name'    => 'queryType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveQueryType'],
            ]))
            ->addField(new Field([
                'name'    => 'mutationType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveMutationType'],
            ]))
            ->addField(new Field([
                'name'    => 'subscriptionType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveSubscriptionType'],
            ]))
            ->addField(new TypesField())
            ->addField(new Field([
                'name'    => 'directives',
                'type'    => new ListType(new DirectiveType()),
                'resolve' => [$this, 'resolveDirectives'],
            ]));
    }
}
