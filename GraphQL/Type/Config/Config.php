<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:31 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Validator\Exception\ConfigurationException;
use Youshido\GraphQL\Validator\Exception\ValidationException;
use Youshido\GraphQL\Validator\Validator;

class Config
{

    /**
     * @var array
     */
    protected $config = [];

    protected $valid = true;

    protected $contextObject;

    protected $validator;

    /**
     * TypeConfig constructor.
     * @param array $config
     * @param mixed $contextObject
     * @throws ConfigurationException
     * @throws ValidationException
     */
    public function __construct($config, $contextObject = null)
    {
        if (!is_array($config)) {
            throw new ConfigurationException('Config for Type should be an array');
        }

        $this->contextObject = $contextObject;
        $this->config        = $config;
        $this->validator     = new Validator($contextObject);
        if (!$this->validator->validate($this->config, $this->getRules())) {
            throw new ValidationException('Config is not valid for ' . get_class($contextObject). "\n" . implode("\n", $this->validator->getErrorsArray()));
        }
    }

    public function getRules()
    {
        return [];
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->validator->isValid();
    }


}