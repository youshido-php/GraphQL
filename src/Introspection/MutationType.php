<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Field\Field;

class MutationType extends QueryType
{

    public function resolve($value = null, $args = [], $type = null)
    {
        /** @var AbstractSchema|Field $value */
        if ($value instanceof AbstractSchema) {
            return $value->getMutationType()->hasFields() ? $value->getMutationType() : null;
        }

        return $value->getConfig()->getType();
    }

}
