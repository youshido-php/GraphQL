<?php

namespace Youshido\GraphQL\Type\InterfaceType;

use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\AbstractType;

/**
 * Class InterfaceType
 */
final class InterfaceType extends AbstractInterfaceType
{
    /**
     * InterfaceType constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = new InterfaceTypeConfig($config, $this);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function build($config)
    {
    }

    /**
     * @param mixed $object
     *
     * @return AbstractType
     */
    public function resolveType($object)
    {
        return $this->getConfig()->getResolveType()($object);
    }
}
