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
    protected $data = [];

    protected $contextObject;

    protected $validator;

    /**
     * TypeConfig constructor.
     * @param array $configData
     * @param mixed $contextObject
     * @throws ConfigurationException
     * @throws ValidationException
     */
    public function __construct($configData, $contextObject = null)
    {
        if (!is_array($configData)) {
            throw new ConfigurationException('Config for Type should be an array');
        }

        $this->contextObject = $contextObject;
        $this->data          = $configData;
        $this->validator     = new Validator($contextObject);
        if (!$this->validator->validate($this->data, $this->getRules())) {
            throw new ValidationException('Config is not valid for ' . get_class($contextObject) . "\n" . implode("\n", $this->validator->getErrorsArray()));
        }
        $this->setupType();
    }

    protected function setupType()
    {
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