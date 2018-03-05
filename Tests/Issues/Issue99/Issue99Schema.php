<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

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
    public function build(SchemaConfig $config): void
    {
        $config->setQuery(
            new ObjectType([
                'fields' => [
                    new Field([
                        'name' => 'items',
                        'type' => new ListType(new ObjectType([
                            'fields' => [
                                'id' => new NonNullType(new IdType()),
                                new Field([
                                    'name' => 'custom',
                                    'type' => new ObjectType([
                                        'fields' => [
                                            'value' => new StringType(),
                                        ],
                                    ]),
                                    'args' => [
                                        'argX' => [
                                            'type' => new NonNullType(new InputObjectType([
                                                'fields' => [
                                                    'x' => new NonNullType(new StringType()),
                                                ],
                                            ])),
                                        ],
                                    ],
                                    'resolve' => static function ($source, $args) {
                                        $x = $args['argX']['x'] ?? Issue99Test::BUG_EXISTS_VALUE;

                                        return [
                                            'value' => $x,
                                        ];
                                    },
                                ]),
                            ],
                        ])),
                        'args' => [
                            'example' => new StringType(),
                        ],
                        'resolve' => static function () {
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
