<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2:02 PM 5/13/16
 */

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;

trait ConfigAwareTrait
{

    /** @var AbstractConfig|ObjectTypeConfig|FieldConfig|InputFieldConfig */
    protected $config;

    public function getConfig()
    {
        return $this->config;
    }

    protected function getConfigValue($key, $defaultValue = null)
    {
        return !empty($this->config) ? $this->config->get($key, $defaultValue) : $defaultValue;
    }

    public function getDescription()
    {
        return $this->getConfigValue('description');
    }
}