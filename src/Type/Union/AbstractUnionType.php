<?php

namespace Youshido\GraphQL\Type\Union;

use Youshido\GraphQL\Config\Object\UnionTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Type\AbstractInterfaceTypeInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class AbstractUnionType
 */
abstract class AbstractUnionType extends AbstractType implements AbstractInterfaceTypeInterface
{
    use ConfigAwareTrait, AutoNameTrait;

    protected $isFinal = false;

    /**
     * ObjectType constructor.
     *
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name']  = $this->getName();
            $config['types'] = $this->getTypes();
        }

        $this->config = new UnionTypeConfig($config, $this, $this->isFinal);
    }

    /**
     * @return AbstractObjectType[]|AbstractScalarType[]
     */
    abstract public function getTypes();

    /**
     * @return string
     */
    public function getKind()
    {
        return TypeMap::KIND_UNION;
    }

    /**
     * @return $this
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
        return true;
    }
}
