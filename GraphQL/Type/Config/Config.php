<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:31 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidatorInterface;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;
use Youshido\GraphQL\Validator\Exception\ValidationException;

class Config
{

    /**
     * @var array
     */
    protected $data = [];

    protected $contextObject;

    /** @var ConfigValidatorInterface */
    protected $validator;

    /**
     * TypeConfig constructor.
     * @param array $configData
     * @param mixed $contextObject
     *
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
        $this->validator     = new ConfigValidator($contextObject);
        if (!$this->validator->validate($this->data, $this->getRules())) {
            throw new ConfigurationException('Config is not valid for ' . get_class($contextObject) . "\n" . implode("\n", $this->validator->getErrorsArray()));
        }
        $this->setupType();
    }

    public function getRules()
    {
        return [];
    }

    protected function setupType()
    {
    }

    public function getName()
    {
        return empty($this->data['name']) ? null : $this->data['name'];
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->validator->isValid();
    }


}