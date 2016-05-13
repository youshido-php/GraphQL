<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;


use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractInputField
{

    use ConfigCallTrait;

    /** @var InputFieldConfig */
    protected $config;

    public function __construct($config)
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
        }

        if (empty($config['name'])) {
            $config['name'] = $this->getName();
        }

        if (TypeService::isScalarTypeName($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }

        $this->config = new InputFieldConfig($config);
    }

    /**
     * @return AbstractInputObjectType
     */
    abstract public function getType();

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return InputFieldConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function getDefaultValue()
    {
        return $this->config->getDefaultValue();
    }

    protected function getConfigValue($key, $defaultValue = null)
    {
        return !empty($this->config) ? $this->config->get($key, $defaultValue) : $defaultValue;
    }

    //todo: think about serialize, parseValue methods

}