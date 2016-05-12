<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class Connection
{

    /**
     * @param $config TypeConfigInterface
     */
    public static function addConnectionArgs($config)
    {
        self::addForwardArgs($config);
        self::addBackwardArgs($config);
    }

    /**
     * @param $config TypeConfigInterface
     */
    public static function addForwardArgs($config)
    {
        $config
            ->addArgument('after', TypeMap::TYPE_STRING)
            ->addArgument('first', TypeMap::TYPE_INT);
    }

    /**
     * @param $config TypeConfigInterface
     */
    public static function addBackwardArgs($config)
    {
        $config
            ->addArgument('before', TypeMap::TYPE_STRING)
            ->addArgument('last', TypeMap::TYPE_INT);
    }

    /**
     * @param AbstractObjectType $type
     * @param null|string        $name
     * @param array              $edgeFields
     * @param array              $connectionFields
     *
     * @return ObjectType
     */
    public function connectionDefinition($type, $name = null, $edgeFields = [], $connectionFields = [])
    {
        $name = $name ?: $type->getName();

        $edgeType = new ObjectType([
            'name'        => $name . 'Edge',
            'description' => 'An edge in a connection.',
            'fields'      => array_merge([
                'node'   => [
                    'type'        => $type,
                    'description' => 'The item at the end of the edge'
                ],
                'cursor' => [
                    'type'        => TypeMap::TYPE_STRING,
                    'description' => 'A cursor for use in pagination'
                ]
            ], $edgeFields)
        ]);

        $connectionType = new ObjectType([
            'name'        => $name . 'Connection',
            'description' => 'A connection to a list of items.',
            'fields'      => array_merge([
                'pageInfo' => [
                    'type'        => new NonNullType(self::getPageInfoType()),
                    'description' => 'Information to aid in pagination.'
                ],
                'edges'    => [
                    'type'        => new ListType($edgeType),
                    'description' => 'A list of edges.'
                ]
            ], $connectionFields)
        ]);

        return $connectionType;
    }

    public static function getPageInfoType()
    {
        //todo: maybe use object singleton - Maybe it's time to use Factory anyway ? :)

        return new ObjectType([
            'name'        => 'PageInfo',
            'description' => 'Information about pagination in a connection.',
            'fields'      => [
                'hasNextPage'     => [
                    'type'        => new NonNullType(TypeMap::TYPE_BOOLEAN),
                    'description' => 'When paginating forwards, are there more items?'
                ],
                'hasPreviousPage' => [
                    'type'        => new NonNullType(TypeMap::TYPE_BOOLEAN),
                    'description' => 'When paginating backwards, are there more items?'
                ],
                'startCursor'     => [
                    'type'        => TypeMap::TYPE_STRING,
                    'description' => 'When paginating backwards, the cursor to continue.'
                ],
                'endCursor'       => [
                    'type'        => TypeMap::TYPE_STRING,
                    'description' => 'When paginating forwards, the cursor to continue.'
                ],
            ]
        ]);
    }

}
