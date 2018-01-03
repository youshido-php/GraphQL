<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Type\InputTypeInterface;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;

/**
 * Class AbstractInputField
 */
abstract class AbstractInputField implements InputFieldInterface
{
    use FieldsArgumentsAwareObjectTrait, AutoNameTrait; //todo: without arguments

    /**
     * AbstractInputField constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
            $config['name'] = $this->getName();
        }

        $this->config = new InputFieldConfig($config, $this);
        $this->build($this->config);

        $this->config->validate();
    }

    /**
     * @param InputFieldConfig $config
     */
    public function build(InputFieldConfig $config)
    {
    }

    /**
     * @return InputTypeInterface
     */
    abstract public function getType();

    /**
     * @return callable|mixed|null
     */
    public function getDefaultValue()
    {
        return $this->config->getDefaultValue();
    }
}
