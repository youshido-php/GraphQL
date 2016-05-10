<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\TypeMap;

class Node
{

    /**
     * @param $id
     *
     * @return array with type and id element
     */
    public static function fromGlobalId($id)
    {
        return explode(':', base64_decode($id));
    }

    /**
     * @param $typeName string name of type
     * @param $id       int local id
     *
     * @return string global id
     */
    public static function toGlobalId($typeName, $id)
    {
        return base64_encode(implode(':', [$typeName, $id]));
    }

    /**
     * @param $config TypeConfigInterface
     */
    public static function addGlobalId($config)
    {
        $config->addField('id', new NonNullType(TypeMap::TYPE_ID), [
            'description' => 'The ID of an object',
            'resolve'     => function ($value = null, $args = [], $type = null) {
                return self::toGlobalId($type->getName() ?: get_class($type), $value);
            }
        ]);
    }

}