<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class MutationType extends QueryType
{

    public function resolve($value = null, $args = [])
    {
        /** @var Schema|Field  $value */
        if($value instanceof Schema){
            return $value->getMutationType();
        }

        return $value->getConfig()->getType();
    }

}