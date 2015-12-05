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

class InterfaceType extends AbstractType
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

    public function getPossibleTypes()
    {

    }

    public function isPossibleType()
    {

    }

    public function getObjectType()
    {

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