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
 * created: 12/1/15 11:07 PM
 */

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Field\InputField;

trait ArgumentsAwareConfigTrait
{
    protected $arguments = [];

    protected $_isArgumentsBuilt;

    public function buildArguments(): void
    {
        if ($this->_isArgumentsBuilt) {
            return;
        }

        if (!empty($this->data['args'])) {
            $this->addArguments($this->data['args']);
        }
        $this->_isArgumentsBuilt = true;
    }

    public function addArguments($argsList)
    {
        foreach ($argsList as $argumentName => $argumentInfo) {
            if ($argumentInfo instanceof InputField) {
                $this->arguments[$argumentInfo->getName()] = $argumentInfo;

                continue;
            }

            $this->addArgument($argumentName, $this->buildConfig($argumentName, $argumentInfo));
        }

        return $this;
    }

    public function addArgument($argument, $argumentInfo = null)
    {
        if (!($argument instanceof InputField)) {
            $argument = new InputField($this->buildConfig($argument, $argumentInfo));
        }
        $this->arguments[$argument->getName()] = $argument;

        return $this;
    }

    /**
     * @param $name
     *
     * @return InputField
     */
    public function getArgument($name)
    {
        return $this->hasArgument($name) ? $this->arguments[$name] : null;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasArgument($name)
    {
        return \array_key_exists($name, $this->arguments);
    }

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

    public function removeArgument($name)
    {
        if ($this->hasArgument($name)) {
            unset($this->arguments[$name]);
        }

        return $this;
    }

    protected function buildConfig($name, $info = null)
    {
        if (!\is_array($info)) {
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
