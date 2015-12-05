<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:07 PM
*/

namespace Youshido\GraphQL\Type\Config\Traits;


use Youshido\GraphQL\Type\Field\InputField;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

trait ArgumentsAwareTrait
{
    protected $arguments = [];

    public function buildArguments()
    {
        $sourceArguments = empty($this->data['arguments']) ? [] : $this->data['arguments'];
        foreach ($sourceArguments as $argumentName => $argumentInfo) {
            $this->addArgument($argumentName, $argumentInfo['type'], $argumentInfo);
        }
    }

    public function addArgument($name, $type, $config = [])
    {
        if (!TypeMap::isInputType($type)) {
            throw new ConfigurationException('Argument input type ' . $type . ' is not supported');
        }

        $config['name'] = $name;
        $config['type'] = is_string($type) ? TypeMap::getScalarTypeObject($type) : $type;

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

    /**
     * @return InputField[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }


}