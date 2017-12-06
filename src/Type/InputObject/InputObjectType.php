<?php

namespace Youshido\GraphQL\Type\InputObject;

use Youshido\GraphQL\Config\Object\InputObjectTypeConfig;

/**
 * Class InputObjectType
 */
final class InputObjectType extends AbstractInputObjectType
{
    /**
     * InputObjectType constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = new InputObjectTypeConfig($config, $this, true);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function build($config)
    {
    }
}
