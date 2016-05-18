<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay\Connection;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class Connection
{

    public static function connectionArgs()
    {
        return array_merge(self::forwardArgs(), self::backwardArgs());
    }

    public static function forwardArgs()
    {
        return [
            'after' => ['type' => TypeMap::TYPE_STRING],
            'first' => ['type' => TypeMap::TYPE_INT]
        ];
    }

    public static function backwardArgs()
    {
        return [
            'before' => ['type' => TypeMap::TYPE_STRING],
            'last'   => ['type' => TypeMap::TYPE_INT]
        ];
    }

    /**
     * @param AbstractType $type
     * @param null|string  $name
     * @param array        $config
     * @option string  edgeFields
     *
     * @return ObjectType
     */
    public static function edgeDefinition(AbstractType $type, $name = null, $config = [])
    {
        $name       = $name ?: $type->getName();
        $edgeFields = !empty($config['edgeFields']) ? $config['edgeFields'] : [];

        $edgeType = new ObjectType([
            'name'        => $name . 'Edge',
            'description' => 'An edge in a connection.',
            'fields'      => array_merge([
                'node'   => [
                    'type'        => $type,
                    'description' => 'The item at the end of the edge',
                    'resolve'     => function ($value) {
                        return $value['node'];
                    }
                ],
                'cursor' => [
                    'type'        => TypeMap::TYPE_STRING,
                    'description' => 'A cursor for use in pagination'
                ]
            ], $edgeFields)
        ]);

        return $edgeType;
    }

    /**
     * @param AbstractType $type
     * @param null|string  $name
     * @param array        $config
     * @option string  connectionFields
     *
     * @return ObjectType
     */
    public static function connectionDefinition(AbstractType $type, $name = null, $config = [])
    {
        $name             = $name ?: $type->getName();
        $connectionFields = !empty($config['connectionFields']) ? $config['connectionFields'] : [];

        $connectionType = new ObjectType([
            'name'        => $name . 'Connection',
            'description' => 'A connection to a list of items.',
            'fields'      => array_merge([
                'pageInfo' => [
                    'type'        => new NonNullType(self::getPageInfoType()),
                    'description' => 'Information to aid in pagination.',
                    'resolve'     => function ($value) {
                        return isset($value['pageInfo']) ? $value['pageInfo'] : null;
                    }
                ],
                'edges'    => [
                    'type'        => new ListType(self::edgeDefinition($type, $name, $config)),
                    'description' => 'A list of edges.',
                    'resolve'     => function ($value) {
                        return isset($value['edges']) ? $value['edges'] : null;
                    }
                ]
            ], $connectionFields)
        ]);

        return $connectionType;
    }

    public static function getPageInfoType()
    {
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

    public static function connectionFromArray(array $data, array $args)
    {
        return self::connectionFromArraySlice($data, $args, 0, count($data));
    }

    public static function connectionFromArraySlice(array $data, array $args, $sliceStart, $arrayLength)
    {
        $after  = isset($args['after']) ? $args['after'] : null;
        $before = isset($args['before']) ? $args['before'] : null;
        $first  = isset($args['first']) ? $args['first'] : null;
        $last   = isset($args['last']) ? $args['last'] : null;

        $sliceEnd = $sliceStart + count($data);

        $beforeOffset = ArrayConnection::cursorToOffsetWithDefault($before, $arrayLength);
        $afterOffset  = ArrayConnection::cursorToOffsetWithDefault($after, -1);

        $startOffset = max($sliceStart - 1, $afterOffset, -1) + 1;
        $endOffset   = min($sliceEnd, $beforeOffset, $arrayLength);

        if ($first) {
            $endOffset = min($endOffset, $startOffset + $first);
        }

        if ($last) {
            $startOffset = max($startOffset, $endOffset - $last);
        }

        $slice = array_slice($data, max($startOffset - $sliceStart, 0), count($data) - ($sliceEnd - $endOffset));
        $edges = array_map(function ($value, $index) {
            return [
                'cursor' => ArrayConnection::offsetToCursor($index),
                'node'   => $value
            ];
        }, $slice, array_keys($slice));

        $firstEdge  = array_key_exists(0, $edges) ? $edges[0] : null;
        $lastEdge   = count($edges) > 0 ? $edges[count($edges) - 1] : null;
        $lowerBound = $after ? $afterOffset + 1 : 0;
        $upperBound = $before ? $beforeOffset : $arrayLength;

        return [
            'edges'    => $edges,
            'pageInfo' => [
                'startCursor'     => $firstEdge ? $firstEdge['cursor'] : null,
                'endCursor'       => $lastEdge ? $lastEdge['cursor'] : null,
                'hasPreviousPage' => $last ? $startOffset > $lowerBound : false,
                'hasNextPage'     => $first ? $endOffset < $upperBound : false
            ]
        ];
    }

}
