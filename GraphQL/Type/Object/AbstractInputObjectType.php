<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/2/15 9:00 PM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

abstract class AbstractInputObjectType extends AbstractObjectType
{
    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config) && (get_class($this) != 'Youshido\GraphQL\Type\Object\InputObjectType')) {
            $config['name']   = $this->getName();
            $config['output'] = $this->getOutputType();
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

    public function isValidValue($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $requiredFields = array_filter($this->getConfig()->getFields(), function ($field) {
            /** @var Field $field */
            return $field->getConfig()->isRequired();
        });

        foreach ($value as $valueKey => $valueItem) {
            if (!$this->getConfig()->hasField($valueKey) || !$this->getConfig()->getField($valueKey)->getType()->isValidValue($valueItem)) {
                return false;
            }

            if (array_key_exists($valueKey, $requiredFields)) {
                unset($requiredFields[$valueKey]);
            }
        }

        return !(count($requiredFields) > 0);
    }

}