<?php

namespace Youshido\GraphQL\Type\Union;

use Youshido\GraphQL\Config\Object\UnionTypeConfig;
use Youshido\GraphQL\Type\AbstractInterfaceTypeInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Type\TypeKind;

/**
 * Class AbstractUnionType
 */
abstract class AbstractUnionType extends AbstractType implements AbstractInterfaceTypeInterface
{
    use ConfigAwareTrait, AutoNameTrait;

    /**
     * ObjectType constructor.
     *
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name']        = $this->getName();
            $config['types']       = $this->getTypes();
            $config['resolveType'] = [$this, 'resolveType'];
        }

        $this->config = new UnionTypeConfig($config, $this);
        $this->config->validate();
    }

    /**
     * @return AbstractObjectType[]
     */
    abstract public function getTypes();

    /**
     * @return string
     */
    public function getKind()
    {
        return TypeKind::KIND_UNION;
    }
}
