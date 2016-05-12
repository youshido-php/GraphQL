<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
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
     * @param $name   string
     */
    public static function addGlobalId($config, $name = '')
    {
        $config->addField('id', new NonNullType(TypeMap::TYPE_ID), [
            'description' => 'The ID of an object',
            'resolve'     => function ($value = null, $args = [], $type = null) use ($name) {
                $name = $name ?: $type->getName();

                return self::toGlobalId($name ?: get_class($type), $value);
            }
        ]);
    }

    /**
     * @return array
     */
    public static function getGlobalId()
    {
        return [
            'type'        => new NonNullType(TypeMap::TYPE_ID),
            'description' => 'The ID of an object'
        ];
    }

    /**
     * @param $config TypeConfigInterface
     */
    public static function addNodeField($config) //todo: here must be fetcher like argument
    {
        $config->addField('node', new NodeInterface(), [
            'args' => [
                'id' => [
                    'type'        => new NonNullType(TypeMap::TYPE_INT),
                    'description' => 'The ID of an object'
                ],
            ]
        ]);
    }

}