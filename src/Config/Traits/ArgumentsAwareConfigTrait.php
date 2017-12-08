<?php

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Field\InputField;

/**
 * Trait ArgumentsAwareConfigTrait
 */
trait ArgumentsAwareConfigTrait
{
    protected $arguments = [];
    protected $_isArgumentsBuilt;

    /**
     * Build object arguments
     */
    public function buildArguments()
    {
        if ($this->_isArgumentsBuilt) {
            return;
        }

        if (!empty($this->data['args'])) {
            $this->addArguments($this->data['args']);
        }
        $this->_isArgumentsBuilt = true;
    }

    /**
     * @param array $arguments
     *
     * @return $this
     */
    public function addArguments($arguments)
    {
        foreach ((array) $arguments as $argumentName => $argumentInfo) {
            if ($argumentInfo instanceof InputField) {
                $this->arguments[$argumentInfo->getName()] = $argumentInfo;
                continue;
            }
            $this->addArgument($argumentName, $this->buildConfig($argumentName, $argumentInfo));
        }

        return $this;
    }

    /**
     * @param array|string      $argument
     * @param null|array|string $info
     *
     * @return $this
     */
    public function addArgument($argument, $info = null)
    {
        if (!($argument instanceof InputField)) {
            $argument = new InputField($this->buildConfig($argument, $info));
        }
        $this->arguments[$argument->getName()] = $argument;

        return $this;
    }

    /**
     * @param array|string      $name
     * @param null|array|string $info
     *
     * @return array
     */
    protected function buildConfig($name, $info = null)
    {
        if (!is_array($info)) {
            return [
                'type' => $info,
                'name' => $name,
            ];
        }
        if (empty($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }

    /**
     * @param string $name
     *
     * @return InputField
     */
    public function getArgument($name)
    {
        return $this->hasArgument($name) ? $this->arguments[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasArgument($name)
    {
        return array_key_exists($name, $this->arguments);
    }

    /**
     * @return bool
     */
    public function hasArguments()
    {
        return !empty($this->arguments);
    }

    /**
     * @return InputField[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeArgument($name)
    {
        if ($this->hasArgument($name)) {
            unset($this->arguments[$name]);
        }

        return $this;
    }
}
