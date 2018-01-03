<?php

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;

/**
 * Class ObjectType
 */
final class ObjectType extends AbstractObjectType
{
    /**
     * ObjectType constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new ObjectTypeConfig($config, $this);
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
     * @return string
     */
    public function getName()
    {
        return $this->getConfigValue('name');
    }
}
