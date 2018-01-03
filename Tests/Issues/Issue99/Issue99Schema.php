<?php

namespace Youshido\Tests\Issues\Issue99;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue99Schema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->setQuery(
            new ObjectType([
                'name'   => 'Item',
                'fields' => [
                    new Field([
                        'name'    => 'items',
                        'type'    => new ListType(new ObjectType([
                            'name'   => 'Item',
                            'fields' => [
                                'id' => new NonNullType(new IdType()),
                                new Field([
                                    'name'    => 'custom',
                                    'type'    => new ObjectType([
                                        'name'   => 'ValueType',
                                        'fields' => [
                                            'value' => new StringType(),
                                        ],
                                    ]),
                                    'args'    => [
                                        'argX' => [
                                            'type' => new NonNullType(new InputObjectType([
                                                'name'   => 'InputX',
                                                'fields' => [
                                                    'x' => new NonNullType(new StringType()),
                                                ],
                                            ])),
                                        ],
                                    ],
                                    'resolve' => function ($source, $args) {
                                        $x = isset($args['argX']['x']) ? $args['argX']['x'] : Issue99Test::BUG_EXISTS_VALUE;

                                        return [
                                            'value' => $x,
                                        ];
                                    },
                                ]),
                            ],
                        ])),
                        'args'    => [
                            'example' => new StringType(),
                        ],
                        'resolve' => function () {
                            return [
                                ['id' => 1],
                                ['id' => 2],
                                ['id' => 3],
                                ['id' => 4],
                            ];
                        },
                    ]),
                ],
            ])
        );
    }
}
