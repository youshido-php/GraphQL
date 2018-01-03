<?php

namespace Youshido\GraphQL\Type\InterfaceType;

use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeKind;

/**
 * Class AbstractInterfaceType
 */
abstract class AbstractInterfaceType extends AbstractType
{
    use FieldsAwareObjectTrait, AutoNameTrait;

    protected $isBuilt = false;

    /**
     * ObjectType constructor.
     *
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
        }

        $this->config = new InterfaceTypeConfig($config, $this);
    }

    /**
     * @param mixed $object
     *
     * @return AbstractType
     */
    abstract public function resolveType($object);

    /**
     * @param InterfaceTypeConfig $config
     */
    abstract public function build($config);

    /**
     * @return InterfaceTypeConfig
     */
    public function getConfig()
    {
        if (!$this->isBuilt) {
            $this->isBuilt = true;
            $this->build($this->config);
        }

        return $this->config;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return TypeKind::KIND_INTERFACE;
    }

    /**
     * @return AbstractType
     */
    public function getNamedType()
    {
        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        return is_array($value) || null === $value || is_object($value);
    }
}
