<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:59 PM
*/

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class RelayTypeFactory
{
    private static $objectHash = [];


    public static function getEdgeType(AbstractType $type, $config = [])
    {
        $name    = empty($config['name']) ? $type->getName() : $config['name'];
        $hashKey = 'edge-' . $name;

        if (!empty(self::$objectHash[$hashKey])) return self::$objectHash[$hashKey];

        $nodeType = empty($config['nodeType']) ? $type : $config['nodeType'];

        $edgeType = new ObjectType([
            'name'        => $name . 'Edge',
            'description' => 'An edge in a connection.',
            'fields'      => [
                'node'   => [
                    'type'        => $nodeType,
                    'description' => 'The item at the end of the edge'
                ],
                'cursor' => [
                    'type'        => TypeMap::TYPE_STRING,
                    'description' => 'A cursor for use in pagination'
                ]
            ]
        ]);

        self::$objectHash[$hashKey] = $edgeType;

        return $edgeType;
    }

    public static function getConnectionType(AbstractType $type, $config = [])
    {
        $name    = empty($config['name']) ? $type->getName() : $config['name'];
        $hashKey = 'connection-' . $name;
        if (!empty(self::$objectHash[$hashKey])) return self::$objectHash[$hashKey];

        // probably need to process config
        /**
         * var edgeFields = config.edgeFields || {};
         * var connectionFields = config.connectionFields || {};
         * var resolveNode = config.resolveNode;
         * var resolveCursor = config.resolveCursor;
         */

        $connectionType = new ObjectType([
            'name'        => $name . 'Connection',
            'description' => 'A connection to a list of items.',
            'fields'      => [
                'pageInfo' => [
                    'type'        => new NonNullType(Connection::getPageInfoType()),
                    'description' => 'Information to aid in pagination.'
                ],
                'edges'    => [
                    'type'        => new ListType(self::getEdgeType($type, $config)),
                    'description' => 'A list of edges.'
                ]
            ]
        ]);
        Connection::addConnectionArgs($connectionType->getConfig());

        self::$objectHash[$hashKey] = $connectionType;

        return $connectionType;
    }
}
