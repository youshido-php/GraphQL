<?php

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;

/**
 * Trait ConfigAwareTrait
 */
trait ConfigAwareTrait
{
    /** @var AbstractConfig|ObjectTypeConfig|FieldConfig|InputFieldConfig */
    protected $config;

    /** @var array */
    protected $configCache = [];

    /**
     * @return AbstractConfig|FieldConfig|InputFieldConfig|ObjectTypeConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $key
     * @param null   $defaultValue
     *
     * @return mixed
     */
    protected function getConfigValue($key, $defaultValue = null)
    {
        if (array_key_exists($key, $this->configCache)) {
            return $this->configCache[$key];
        }
        $this->configCache[$key] = null !== $this->config ? $this->config->get($key, $defaultValue) : $defaultValue;

        return $this->configCache[$key];
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getConfigValue('description');
    }
}
