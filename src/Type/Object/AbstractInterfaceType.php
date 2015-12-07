<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:12 AM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractInterfaceType extends AbstractType
{


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

    abstract protected function build(InterfaceTypeConfig $config);

    public function resolveType($object)
    {
        return $object;
    }

    public function checkBuild()
    {
        if (!$this->isBuild) {
            $this->isBuild = true;
            $this->build($this->config);
        }
    }

    public function getName()
    {
        return $this->getConfig()->get('name', 'InterfaceType');
    }

    public function getKind()
    {
        return TypeMap::KIND_INTERFACE;
    }

    public function isValidValue($value)
    {
        return true;
    }

}