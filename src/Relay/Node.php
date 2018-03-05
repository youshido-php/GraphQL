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
 * Date: 10.05.16.
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
        if ($decoded = \base64_decode($id, true)) {
            return \explode(':', $decoded);
        }

        return [null, null];
    }

    /**
     * @param $typeName string name of type
     * @param $id       int local id
     *
     * @return string global id
     */
    public static function toGlobalId($typeName, $id)
    {
        return \base64_encode(\implode(':', [$typeName, $id]));
    }
}
