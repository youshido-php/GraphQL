<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL;


use Youshido\GraphQL\Type\Config\FieldConfig;
use Youshido\GraphQL\Type\TypeInterface;

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

    public function getConfig()
    {
        return $this->config;
    }

}