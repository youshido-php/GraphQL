<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Field;


use Youshido\GraphQL\Type\Config\Field\FieldConfig;

class Field
{

    /** @var FieldConfig */
    protected $config;

    public function __construct($config)
    {
        $this->config = new FieldConfig($config);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->config->getName();
    }

    public function getType()
    {
        return $this->config->getType();
    }

    public function setType($type)
    {
        $this->config->set('type', $type);
    }


    /**
     * @return FieldConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    //todo: rethink logic below

    public function getIsDeprecated()
    {
        return $this->getConfig()->get('isDeprecated', false);
    }

    public function getDeprecationReason()
    {
        return $this->getConfig()->get('deprecationReason', null);
    }

    public function getDescription()
    {
        return $this->getConfig()->get('description', null);
    }
}