<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:31 AM
*/

namespace Youshido\GraphQL\Config;


use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidatorInterface;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;
use Youshido\GraphQL\Validator\Exception\ValidationException;

/**
 * Class Config
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

    protected $extraFieldsAllowed = null;

    /** @var ConfigValidatorInterface */
    protected $validator;

    /**
     * TypeConfig constructor.
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
        $this->validator     = new ConfigValidator($contextObject);

        if (!$this->validator->validate($this->data, $this->getContextRules(), $this->extraFieldsAllowed)) {
            throw new ConfigurationException('Config is not valid for ' . ($contextObject ? get_class($contextObject) : null) . "\n" . implode("\n", $this->validator->getErrorsArray(false)));
        }

        $this->build();
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

    /**
     * @return null|callable
     */
    public function getResolveFunction()
    {
        return $this->get('resolve', null);
    }

    protected function build()
    {
    }

    /**
     * @param      $key
     * @param null $defaultValue
     * @return mixed|null|callable
     */
    public function get($key, $defaultValue = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $defaultValue;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'get') {
            $propertyName = lcfirst(substr($method, 3));
        } elseif (substr($method, 0, 3) == 'set') {
            $propertyName = lcfirst(substr($method, 3));
            $this->set($propertyName, $arguments[0]);

            return $this;
        } elseif (substr($method, 0, 2) == 'is') {
            $propertyName = lcfirst(substr($method, 2));
        } else {
            throw new \Exception('Call to undefined method ' . $method);
        }

        return $this->get($propertyName);
    }


}
