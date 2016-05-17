<?php

namespace Examples\StarWars;

use Youshido\GraphQL\Relay\Field\NodeField;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class StarWarsRelaySchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()
               ->addField('rebels', [
                   'type'    => new FactionType(),
                   'resolve' => function () {
                       return TestDataProvider::getFaction('rebels');
                   }
               ])->addField('empire', [
                'type'    => new FactionType(),
                'resolve' => function () {
                    return TestDataProvider::getFaction('empire');
                }
            ])->addField(new NodeField());

        /** I want to get to the point where it works the same as JS and then go with OOP approach */
//        $config->getMutation()->addField('introduceShip', [
//            'args' => [
//                'shipName'  => new NonNullType(new StringType()),
//                'factionId' => new NonNullType(new IdType()),
//            ],
//            'fields' => [
//                'ship' => [
//                    'type' => new ShipType(),
//                    'resolve' => function($value, $args) {
//                        return TestDataProvider::getShip($args['shipId']);
//                    }
//                ],
//                'faction' => [
//                    'type' => new FactionType(),
//                    'resolve' => function($value, $args) {
//                        return TestDataProvider::getShip($args['factionId']);
//                    }
//                ],
//            ],
//            'mutateAndGetPayload' => function($value) {
//                $newShip = TestDataProvider::createShip($value['name'], $value['factionId']);
//                return [
//                    'shipId' => $newShip['id'],
//                    'factionId' => $value['factionId']
//                ];
//            }
//        ]);
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
