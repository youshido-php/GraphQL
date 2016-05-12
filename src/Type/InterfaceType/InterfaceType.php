<?php
/**
 * Date: 12.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\InterfaceType;


use Youshido\GraphQL\Config\TypeConfigInterface;

final class InterfaceType extends AbstractInterfaceType
{

    public function resolveType($object)
    {
        return $this->getConfig()->resolveType($object);
    }

    /**
     * @param TypeConfigInterface $config
     * @return mixed
     */
    public function build($config)
    {
    }
}