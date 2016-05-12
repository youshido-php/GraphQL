<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:12 AM
*/

namespace Youshido\GraphQL\Type\InterfaceType;


use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractInterfaceType extends AbstractType
{
    use ConfigCallTrait, AutoNameTrait;

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
        }

        $this->config = new InterfaceTypeConfig($config, $this);
    }

    abstract public function resolveType($object);

    public function getKind()
    {
        return TypeMap::KIND_INTERFACE;
    }

    public function getNamedType()
    {
        return $this;
    }

    public function isValidValue($value)
    {
        if ($value instanceof AbstractObjectType) {
            foreach ($value->getInterfaces() as $interface) {
                if ($interface instanceof $this) return true;
            }
        }

        return false;
    }

}
