<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:12 AM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Object\UnionTypeConfig;
use Youshido\GraphQL\Type\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractUnionType extends AbstractType
{
    use ConfigCallTrait, AutoNameTrait;

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name']  = $this->getName();
            $config['types'] = $this->getTypes();
        }

        $this->config = new UnionTypeConfig($config, $this);
    }

    abstract public function getTypes();

    abstract public function resolveType($object);

    public function checkBuild()
    {
        if (!$this->isBuild) {
            $this->isBuild = true;
            $this->build($this->config);
        }
    }



    public function build(TypeConfigInterface $config)
    {

    }

    public function resolve($value = null, $args = [])
    {

    }

    public function getKind()
    {
        return TypeMap::KIND_UNION;
    }

    public function getNamedType()
    {
        return $this;
    }

    public function isValidValue($value)
    {
        return true;
    }

}
