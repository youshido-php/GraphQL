<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Type\InputTypeInterface;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class AbstractInputField
 */
abstract class AbstractInputField implements InputFieldInterface
{
    use FieldsArgumentsAwareObjectTrait, AutoNameTrait;

    /** @var bool */
    protected $isFinal = false;

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

        if (TypeService::isScalarType($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }

        $this->config = new InputFieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
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
