<?php

namespace Youshido\GraphQL\Config;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;

/**
 * Class Config
 */
abstract class AbstractConfig
{
    /** @var array */
    protected $data = [];

    /** @var mixed|null */
    protected $contextObject;

    /** @var bool */
    private $validated = false;

    /**
     * TypeConfig constructor.
     *
     * @param array $data
     * @param mixed $contextObject
     */
    public function __construct(array $data, $contextObject = null)
    {
        $this->contextObject = $contextObject;
        $this->data          = $data;

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
        if ($this->validated) {
            return;
        }

        $validator = ConfigValidator::getInstance();

        if (!$validator->validate($this)) {
            throw new ConfigurationException(sprintf('Config is not valid for %s: %s',
                $this->contextObject ? get_class($this->contextObject) : '',
                implode("\n", $validator->getErrorsArray())
            ));
        }

        $this->validated = true;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed|null
     */
    public function getContextObject()
    {
        return $this->contextObject;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        $rules = $this->getRules();
        if (isset($rules[$key]) && array_key_exists('default', $rules[$key])) {
            return $rules[$key]['default'];
        }

        return null;
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
     * @return callable|mixed|null
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        if (0 === strpos($method, 'get')) {
            $propertyName = lcfirst(substr($method, 3));
        } elseif (0 === strpos($method, 'set')) {
            $propertyName = lcfirst(substr($method, 3));
            $this->set($propertyName, $arguments[0]);
        } elseif (0 === strpos($method, 'is')) {
            $propertyName = lcfirst($method);
        } else {
            throw new ConfigurationException('Call to undefined method ' . $method);
        }

        return $this->get($propertyName);
    }

    protected function build()
    {
    }
}
