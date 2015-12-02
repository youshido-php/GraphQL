<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 10:25 PM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\Config\InputObjectTypeConfig;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class InputObjectType extends ObjectType
{
    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config) && (get_class($this) != 'Youshido\GraphQL\Type\Object\InputObjectType')) {
            $config['name'] = $this->getName();
            $config['output_type'] = $this->getOutputType();
        }

        $this->config = new InputObjectTypeConfig($config, $this);
        $this->name   = $this->config->getName();

        $this->buildFields($this->config);
        $this->buildArguments($this->config);
    }

    public function getOutputType()
    {
        throw new ConfigurationException('You must define output type');
    }
}