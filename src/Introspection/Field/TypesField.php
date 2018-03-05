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
use Youshido\GraphQL\Introspection\QueryType;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class TypesField extends AbstractField
{
    use TypeCollectorTrait;

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return new ListType(new QueryType());
    }

    public function getName()
    {
        return 'types';
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        /* @var $value AbstractSchema $a */
        $this->types = [];
        $this->collectTypes($value->getQueryType());

        if ($value->getMutationType()->hasFields()) {
            $this->collectTypes($value->getMutationType());
        }

        foreach ($value->getTypesList()->getTypes() as $type) {
            $this->collectTypes($type);
        }

        return \array_values($this->types);
    }
}
