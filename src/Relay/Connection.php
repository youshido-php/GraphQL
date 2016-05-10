<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
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

}