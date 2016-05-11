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
        $config->addField('id', new GlobalIdField('Ship'))
            ->addField('name', TypeMap::TYPE_STRING, ['description' => 'The name of the ship.']);
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        // maybe we need to think of making a non-abstract resolve method on type
        // and adding Field / resolve default executing type resolve... need to be discussed

        //yes, i thought it too, and method build() is not necessary too.
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
