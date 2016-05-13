<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2:02 PM 5/13/16
 */

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Config\AbstractConfig;

trait ConfigAwareTrait
{

    /** @var AbstractConfig */
    protected $config;

    public function getConfig()
    {
        return $this->config;
    }

    protected function getConfigValue($key, $defaultValue = null)
    {
        return !empty($this->config) ? $this->config->get($key, $defaultValue) : $defaultValue;
    }
}