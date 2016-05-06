<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Type\Field\Field;

class MutationType extends QueryType
{

    public function resolve($value = null, $args = [], $type = null)
    {
        /** @var AbstractSchema|Field $value */
        if ($value instanceof AbstractSchema) {
            $res = $value->getMutationType()->hasFields() ? $value->getMutationType() : null;
//            $res = $value->getMutationType();
            return $res;
        }

        return $value->getConfig()->getType();
    }

}
