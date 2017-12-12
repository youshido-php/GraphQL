<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Config\Traits\ResolvableObjectTrait;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class AbstractField
 */
abstract class AbstractField implements FieldInterface
{
    use FieldsArgumentsAwareObjectTrait, ResolvableObjectTrait, AutoNameTrait {
        getName as getAutoName;
    }

    /** @var bool */
    protected $isFinal = false;

    /** @var bool|string */
    private $nameCache;

    /**
     * AbstractField constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
            $config['name'] = $this->getName();

            if (empty($config['name'])) {
                $config['name'] = $this->getAutoName();
            }
        }

        if (TypeService::isScalarType($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }
        $this->nameCache = isset($config['name']) ? $config['name'] : $this->getAutoName();

        $this->config = new FieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    /**
     * @return AbstractType|AbstractObjectType
     */
    abstract public function getType();

    /**
     * @param FieldConfig $config
     */
    public function build(FieldConfig $config)
    {
    }

    /**
     * @param AbstractType|AbstractObjectType $type
     */
    public function setType($type)
    {
        $this->getConfig()->set('type', $type);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->nameCache;
    }

    /**
     * @return bool
     */
    public function isDeprecated()
    {
        return $this->getConfigValue('isDeprecated', false);
    }

    /**
     * @return string
     */
    public function getDeprecationReason()
    {
        return $this->getConfigValue('deprecationReason');
    }
}
