<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:53 PM
*/

namespace Examples\StarWars;


use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Relay\NodeInterface;
use Youshido\GraphQL\Relay\RelayTypeFactory;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class FactionType extends AbstractObjectType
{

    public function build($config)
    {
        $config->addField('id', new GlobalIdField('Faction'))
               ->addField('factionId', TypeMap::TYPE_STRING, [
                   'description' => 'id of faction id db',
                   'resolve'     => function ($value = null, $args = [], $type = null) {
                       return !empty($value['id']) ? $value['id'] : null;
                   }
               ])
               ->addField('name', TypeMap::TYPE_STRING, [
                   'description' => 'The name of the faction.'
               ])
               ->addField('ships', RelayTypeFactory::getConnectionType($this), [
                   'description' => 'The ships used by the faction',
                   'resolve' => function($value = null, $args = [], $type = null) {
                       /**
                        *       resolve: (faction, args) => connectionFromArray(
                       faction.ships.map((id) => getShip(id)),
                       args
                       ),
                        */
                       return [];
                   }
               ]);

    }

    public function resolve($value = null, $args = [], $type = null)
    {
    }


    public function getDescription()
    {
        return 'A faction in the Star Wars saga';
    }

    public function getInterfaces()
    {
        return [new NodeInterface()];
    }

}
