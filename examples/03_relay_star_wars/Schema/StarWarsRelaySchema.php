<?php

namespace Examples\StarWars;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Config\Schema\SchemaConfig;

class StarWarsRelaySchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $queryTypeConfig = $config->getQuery()->getConfig();

        Node::addNodeField($queryTypeConfig);

        $queryTypeConfig->addField('faction', new FactionType());
//        $queryTypeConfig->addField('latestShip', new LatestShipField());
//
//        $queryTypeConfig->addField('myShips', new ListType(new ShipType()));
//        $queryTypeConfig->addField('availableShipsToBuy', new ListType(new ShipType()));
//
//        $queryTypeConfig->addField(new MyShipsField());
        // getType()
        // getArguments()


//        $queryTypeConfig->addField('latestShip', new ShipType(), ['resolve' => '@helper.ship']);
//        $queryTypeConfig->addField('ship', new ShipType());
//
//        $queryTypeConfig->addField(new AvailableShipsToBuyField());
//
//
//
//        $queryTypeConfig->addField('ship', new ShipType(), [
//            'args' => [],
//            'resolve' => function() {}
//        ]);
    }

}
