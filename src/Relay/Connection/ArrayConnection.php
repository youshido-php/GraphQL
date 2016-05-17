<?php
/**
 * Date: 17.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQl\Relay\Connection;


class ArrayConnection
{

    const PREFIX = 'arrayconnection:';

    /**
     * @param $offset int
     * @return string
     */
    public static function offsetToCursor($offset)
    {
        return base64_encode(self::PREFIX . $offset);
    }

    /**
     * @param $cursor string
     *
     * @return int|null
     */
    public static function cursorToOffset($cursor)
    {
        if ($decoded = base64_decode($cursor)) {
            return (int)substr($decoded, strlen(self::PREFIX));
        }

        return null;
    }

    public static function cursorToOffsetWithDefault($cursor, $default)
    {
        if (!is_string($cursor)) {
            return $default;
        }

        $offset = self::cursorToOffset($cursor);

        return is_null($offset) ? $default : $offset;
    }

}