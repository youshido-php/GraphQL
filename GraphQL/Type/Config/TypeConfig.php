<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:31 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class TypeConfig
{

    /**
     * @var array
     */
    protected $config = [];

    /**
     * TypeConfig constructor.
     * @param array $config
     * @throws ConfigurationException
     */
    public function __construct($config = [])
    {
        if (!is_array($config)) {
            throw new ConfigurationException('Config for Type should be an array');
        }

        $this->config = $config;
        $this->validateConfig();
    }

    public function validateConfig()
    {

    }

    public function getRules()
    {
        return [];
    }

}