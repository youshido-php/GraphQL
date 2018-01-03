<?php

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Field\InputFieldInterface;

/**
 * Trait ArgumentsAwareConfigTrait
 */
trait ArgumentsAwareConfigTrait
{
    /**
     * @param array $arguments
     *
     * @return $this
     */
    public function addArguments($arguments)
    {
        foreach ((array) $arguments as $argumentName => $argumentInfo) {
            if ($argumentInfo instanceof InputField) {
                $this->data['args'][$argumentInfo->getName()] = $argumentInfo;

                continue;
            }

            $this->addArgument($argumentName, $this->buildConfig($argumentName, $argumentInfo));
        }

        return $this;
    }

    /**
     * @param array|string|InputFieldInterface $argument
     * @param null|array|string                $info
     *
     * @return $this
     */
    public function addArgument($argument, $info = null)
    {
        if (!($argument instanceof InputField)) {
            $argument = new InputField($this->buildConfig($argument, $info));
        }

        $this->data['args'][$argument->getName()] = $argument;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return InputField
     */
    public function getArgument($name)
    {
        return $this->hasArgument($name) ? $this->data['args'][$name] : null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasArgument($name)
    {
        return isset($this->data['args'][$name]);
    }

    /**
     * @return bool
     */
    public function hasArguments()
    {
        return !empty($this->data['args']);
    }

    /**
     * @return InputField[]
     */
    public function getArguments()
    {
        if ($this->hasArguments()) {
            return (array) $this->data['args'];
        }

        return [];
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeArgument($name)
    {
        if ($this->hasArgument($name)) {
            unset($this->data['args'][$name]);
        }

        return $this;
    }

    /**
     * Build object arguments
     */
    protected function buildArguments()
    {
        if (!empty($this->data['args'])) {
            $args               = $this->data['args'];
            $this->data['args'] = [];

            $this->addArguments($args);
        }
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
}
