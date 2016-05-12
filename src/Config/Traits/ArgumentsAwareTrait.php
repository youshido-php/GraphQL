<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:07 PM
*/

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

trait ArgumentsAwareTrait
{
    protected $arguments = [];
    protected $_isArgumentBuilt;

    public function buildArguments()
    {
        if ($this->_isArgumentBuilt) return true;
        $sourceArguments = empty($this->data['args']) ? [] : $this->data['args'];
        foreach ($sourceArguments as $argumentName => $argumentInfo) {
            if ($argumentInfo instanceof InputField) {
                $this->arguments[$argumentName] = $argumentInfo;
                continue;
            } elseif ($argumentInfo instanceof AbstractType) {
                $config = [
                    'type' => $argumentInfo
                ];
                $this->addArgument($argumentName, $argumentInfo, $config);
            } else {
                $this->addArgument($argumentName, $argumentInfo['type'], $argumentInfo);
            }
        }
        $this->_isArgumentBuilt = true;
    }

    public function addArgument($name, $type, $config = [])
    {
        if (!TypeService::isInputType($type)) {
            throw new ConfigurationException('Argument input type ' . $type . ' is not supported');
        }

        $config['name'] = $name;
        $config['type'] = is_string($type) ? TypeFactory::getScalarType($type) : $type;

        $this->arguments[$name] = new InputField($config);

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
        return array_key_exists($name, $this->arguments);
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
    }

}
