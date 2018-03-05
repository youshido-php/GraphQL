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
 * Date: 17.05.16.
 */

namespace Youshido\GraphQL\Relay\Connection;

class ArrayConnection
{
    public const PREFIX = 'arrayconnection:';

    public static function cursorForObjectInConnection($data, $object)
    {
        if (!\is_array($data)) {
            return;
        }

        $index = \array_search($object, $data, true);

        return false === $index ? null : (string) self::keyToCursor($index);
    }

    /**
     * @param $offset int
     *
     * @return string
     *
     * @deprecated
     *   Use keyToCursor instead.
     */
    public static function offsetToCursor($offset)
    {
        return self::keyToCursor($offset);
    }

    public static function keyToCursor($key)
    {
        return \base64_encode(self::PREFIX . $key);
    }

    /**
     * @param $cursor string
     *
     * @return int|null
     *
     * @deprecated Use cursorToKey instead.
     */
    public static function cursorToOffset($cursor)
    {
        return self::cursorToKey($cursor);
    }

    /**
     * Converts a cursor to its array key.
     *
     * @param $cursor
     *
     * @return string|null
     */
    public static function cursorToKey($cursor)
    {
        if ($decoded = \base64_decode($cursor, true)) {
            return \mb_substr($decoded, \mb_strlen(self::PREFIX));
        }
    }

    /**
     * Converts a cursor to an array offset.
     *
     * @param $cursor
     *   The cursor string.
     * @param $default
     *   The default value, in case the cursor is not given.
     * @param array $array
     *                     The array to use in counting the offset. If empty, assumed to be an indexed array.
     *
     * @return int|null
     */
    public static function cursorToOffsetWithDefault($cursor, $default, $array = [])
    {
        if (!\is_string($cursor)) {
            return $default;
        }

        $key = self::cursorToKey($cursor);

        if (empty($array)) {
            $offset = $key;
        } else {
            $offset = \array_search($key, \array_keys($array), true);
        }

        return null === $offset ? $default : (int) $offset;
    }

    public static function connectionFromArray(array $data, array $args = [])
    {
        return self::connectionFromArraySlice($data, $args, 0, \count($data));
    }

    public static function connectionFromArraySlice(array $data, array $args, $sliceStart, $arrayLength)
    {
        $after  = $args['after']  ?? null;
        $before = $args['before'] ?? null;
        $first  = $args['first']  ?? null;
        $last   = $args['last']   ?? null;

        $sliceEnd = $sliceStart + \count($data);

        $beforeOffset = self::cursorToOffsetWithDefault($before, $arrayLength, $data);
        $afterOffset  = self::cursorToOffsetWithDefault($after, -1, $data);

        $startOffset = \max($sliceStart - 1, $afterOffset, -1) + 1;
        $endOffset   = \min($sliceEnd, $beforeOffset, $arrayLength);

        if ($first) {
            $endOffset = \min($endOffset, $startOffset + $first);
        }

        if ($last) {
            $startOffset = \max($startOffset, $endOffset - $last);
        }

        $arraySliceStart = \max($startOffset - $sliceStart, 0);
        $arraySliceEnd   = \count($data) - ($sliceEnd - $endOffset) - $arraySliceStart;

        $slice = \array_slice($data, $arraySliceStart, $arraySliceEnd, true);
        $edges = \array_map(['self', 'edgeForObjectWithIndex'], $slice, \array_keys($slice));

        $firstEdge  = \array_key_exists(0, $edges) ? $edges[0] : null;
        $lastEdge   = \count($edges) > 0 ? $edges[\count($edges) - 1] : null;
        $lowerBound = $after ? $afterOffset + 1 : 0;
        $upperBound = $before ? $beforeOffset : $arrayLength;

        return [
            'edges'    => $edges,
            'pageInfo' => [
                'startCursor'     => $firstEdge ? $firstEdge['cursor'] : null,
                'endCursor'       => $lastEdge ? $lastEdge['cursor'] : null,
                'hasPreviousPage' => $last ? $startOffset > $lowerBound : false,
                'hasNextPage'     => $first ? $endOffset < $upperBound : false,
            ],
        ];
    }

    public static function edgeForObjectWithIndex($object, $index)
    {
        return [
            'cursor' => self::keyToCursor($index),
            'node'   => $object,
        ];
    }
}
