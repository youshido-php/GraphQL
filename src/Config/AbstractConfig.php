<?php

namespace Youshido\GraphQL\Config;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Exception\ValidationException;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;

/**
 * Class Config
 *
 * @package Youshido\GraphQL\Config
 */
abstract class AbstractConfig
{
    /**
     * @var array
     */
    protected $data = [];

    protected $contextObject;

    protected $finalClass = false;

    protected $extraFieldsAllowed = false;

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

    /**
     * @return array
     */
    abstract public function getRules();

    /**
     * @throws ConfigurationException
     */
    public function validate()
    {
        $validator = ConfigValidator::getInstance();

        if (!$validator->validate($this->data, $this->getContextRules(), $this->extraFieldsAllowed)) {
            throw new ConfigurationException('Config is not valid for ' . ($this->contextObject ? get_class($this->contextObject) : null) . "\n" . implode("\n", $validator->getErrorsArray(false)));
        }
    }

    /**
     * @return mixed
     */
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function getContextObject()
    {
        return $this->contextObject;
    }

    /**
     * @return bool
     */
    public function isFinalClass()
    {
        return $this->finalClass;
    }

    /**
     * @return bool
     */
    public function isExtraFieldsAllowed()
    {
        return $this->extraFieldsAllowed;
    }

    /**
     * @return null|callable
     */
    public function getResolveFunction()
    {
        return $this->get('resolve', null);
    }

    /**
     * @param string $key
     * @param null   $defaultValue
     *
     * @return mixed|null|callable
     */
    public function get($key, $defaultValue = null)
    {
        return $this->has($key) ? $this->data[$key] : $defaultValue;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param string $method
     * @param  array $arguments
     *
     * @return $this|callable|mixed|null
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        if (0 === strpos($method, 'get')) {
            $propertyName = lcfirst(substr($method, 3));
        } elseif (0 === strpos($method, 'set')) {
            $propertyName = lcfirst(substr($method, 3));
            $this->set($propertyName, $arguments[0]);

            return $this;
        } elseif (0 === strpos($method, 'is')) {
            $propertyName = lcfirst(substr($method, 2));
        } else {
            throw new ConfigurationException('Call to undefined method ' . $method);
        }

        return $this->get($propertyName);
    }

    protected function build()
    {
    }
}
