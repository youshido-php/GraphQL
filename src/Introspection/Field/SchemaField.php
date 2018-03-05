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
 * Date: 16.05.16.
 */

namespace Youshido\GraphQL\Introspection\Field;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Introspection\SchemaType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class SchemaField extends AbstractField
{
    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return new SchemaType();
    }

    public function getName()
    {
        return '__schema';
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $info->getExecutionContext()->getSchema();
    }
}
