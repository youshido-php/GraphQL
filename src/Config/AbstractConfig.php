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
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11/27/15 2:31 AM
 */

namespace Youshido\GraphQL\Config;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Exception\ValidationException;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;

/**
 * Class Config.
 */
abstract class AbstractConfig
{
    /**
     * @var array
     */
    protected $data = [];

    protected $contextObject;

    protected $finalClass = false;

    protected $extraFieldsAllowed;

    /**
     * TypeConfig constructor.
     *
     * @param array $configData
     * @param mixed $contextObject
     * @param bool  $finalClass
     *
     * @throws ConfigurationException
     * @throws ValidationException
     */
    public function __construct(array $configData, $contextObject = null, $finalClass = false)
    {
        if (empty($configData)) {
            throw new ConfigurationException('Config for Type should be an array');
        }

        $this->contextObject = $contextObject;
        $this->data          = $configData;
        $this->finalClass    = $finalClass;

        $this->build();
    }

    public function __call($method, $arguments)
    {
        if ('get' === \mb_substr($method, 0, 3)) {
            return $this->get(\lcfirst(\mb_substr($method, 3)));
        }

        if ('set' === \mb_substr($method, 0, 3)) {
            $propertyName = \lcfirst(\mb_substr($method, 3));
            $this->set($propertyName, $arguments[0]);

            return $this;
        }

        if ('is' === \mb_substr($method, 0, 2)) {
            return $this->get(\lcfirst(\mb_substr($method, 2)));
        }

        throw new \Exception('Call to undefined method ' . $method);
    }

    public function validate(): void
    {
        $validator = ConfigValidator::getInstance();

        if (!$validator->validate($this->data, $this->getContextRules(), $this->extraFieldsAllowed)) {
            throw new ConfigurationException('Config is not valid for ' . ($this->contextObject ? \get_class($this->contextObject) : null) . "\n" . \implode("\n", $validator->getErrorsArray(false)));
        }
    }

    public function getContextRules()
    {
        $rules = $this->getRules();

        if ($this->finalClass) {
            foreach ($rules as $name => $info) {
                if (!empty($info['final'])) {
                    $rules[$name]['required'] = true;
                }
            }
        }

        return $rules;
    }

    abstract public function getRules();

    public function getName()
    {
        return $this->get('name');
    }

    public function getType()
    {
        return $this->get('type');
    }

    public function getData()
    {
        return $this->data;
    }

    public function getContextObject()
    {
        return $this->contextObject;
    }

    public function isFinalClass()
    {
        return $this->finalClass;
    }

    public function isExtraFieldsAllowed()
    {
        return $this->extraFieldsAllowed;
    }

    /**
     * @return callable|null
     */
    public function getResolveFunction()
    {
        return $this->get('resolve', null);
    }

    /**
     * @param      $key
     * @param null $defaultValue
     *
     * @return callable|mixed|null
     */
    public function get($key, $defaultValue = null)
    {
        return $this->has($key) ? $this->data[$key] : $defaultValue;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function has($key)
    {
        return \array_key_exists($key, $this->data);
    }

    protected function build(): void
    {
    }
}
