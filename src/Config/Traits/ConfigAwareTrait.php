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
    /** @var AbstractConfig|FieldConfig|InputFieldConfig|ObjectTypeConfig */
    protected $config;

    protected $configCache = [];

    public function getConfig()
    {
        return $this->config;
    }

    public function getDescription()
    {
        return $this->getConfigValue('description');
    }

    protected function getConfigValue($key, $defaultValue = null)
    {
        if (\array_key_exists($key, $this->configCache)) {
            return $this->configCache[$key];
        }
        $this->configCache[$key] = !empty($this->config) ? $this->config->get($key, $defaultValue) : $defaultValue;

        return $this->configCache[$key];
    }
}
