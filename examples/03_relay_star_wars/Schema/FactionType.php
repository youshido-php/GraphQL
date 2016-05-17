<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:53 PM
*/

namespace Examples\StarWars;


use Youshido\GraphQL\Relay\Connection;
use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Relay\NodeInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class FactionType extends AbstractObjectType
{

    public function build($config)
    {
        $config
            ->addField(new GlobalIdField())
            ->addField('name', [
                'type'        => TypeMap::TYPE_STRING,
                'description' => 'The name of the faction.'
            ])
            ->addField('ships', [
                'type'        => Connection::connectionDefinition(new ShipType()),
                'description' => 'The ships used by the faction',
                'args' => Connection::connectionArgs(),
                'resolve'     => function ($value = null, $args = [], $type = null) {
                    /**
                     *       resolve: (faction, args) => connectionFromArray(
                     * faction.ships.map((id) => getShip(id)),
                     * args
                     * ),
                     */
                    return [];
                }
            ]);

    }

    public function getDescription()
    {
        return 'A faction in the Star Wars saga';
    }

    public function getOne($id)
    {
        return TestDataProvider::getFaction($id);
    }

    public function getInterfaces()
    {
        return [new NodeInterface()];
    }

}
