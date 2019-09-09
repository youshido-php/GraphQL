<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay;


class Node
{

    /**
     * @param $id
     *
     * @return array with type and id element
     */
    public static function fromGlobalId($id)
    {
        $decoded = base64_decode($id, true);
        if (!$decoded) {
            throw new \InvalidArgumentException('ID must be a valid base 64 string');
        }
        $decodedParts = explode(':', $decoded, 2);
        if (count($decodedParts) !== 2) {
            throw new \InvalidArgumentException('ID was not correctly formed');
        }
        return $decodedParts;
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
}
