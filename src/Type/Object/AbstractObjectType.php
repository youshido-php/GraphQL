<?php

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Exception\LogicException;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class AbstractObjectType
 */
abstract class AbstractObjectType extends AbstractType
{
    use AutoNameTrait, FieldsArgumentsAwareObjectTrait;

    protected $isBuilt = false;

    /**
     * ObjectType constructor.
     *
     * @param $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config['name']       = $this->getName();
            $config['interfaces'] = $this->getInterfaces();
        }

        $this->config = new ObjectTypeConfig($config, $this);
    }

    /**
     * @return ObjectTypeConfig
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
     * @param mixed $value
     *
     * @return null
     * @throws LogicException
     */
    final public function serialize($value)
    {
        throw new LogicException('You can not serialize object value directly');
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return TypeMap::KIND_OBJECT;
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->getConfigValue('type', $this);
    }

    /**
     * @return AbstractType
     */
    public function getNamedType()
    {
        return $this;
    }

    /**
     * @param ObjectTypeConfig $config
     */
    abstract public function build($config);

    /**
     * @return AbstractInterfaceType[]
     */
    public function getInterfaces()
    {
        return $this->getConfigValue('interfaces', []);
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
