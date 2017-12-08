<?php

namespace Youshido\GraphQL\Type\Enum;

use Youshido\GraphQL\Config\Object\EnumTypeConfig;

/**
 * Class EnumType
 */
final class EnumType extends AbstractEnumType
{
    /**
     * EnumType constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new EnumTypeConfig($config, $this, true);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->getConfig()->getValues();
    }
}
