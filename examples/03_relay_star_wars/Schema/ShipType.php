<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:17 PM
*/

namespace Examples\StarWars;


use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Relay\NodeInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class ShipType extends AbstractObjectType
{
    public function build($config)
    {
        $config
            ->addField(new GlobalIdField('Ship'))
            ->addField('name', ['type' => TypeMap::TYPE_STRING, 'description' => 'The name of the ship.']);
    }

    public function getOne($id)
    {
        return TestDataProvider::getShip($id);
    }


    public function getDescription()
    {
        return 'A ship in the Star Wars saga';
    }

    public function getInterfaces()
    {
        return [new NodeInterface()];
    }

}
