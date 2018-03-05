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

namespace Youshido\Tests\Issues\Issue90;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;

class Issue90Schema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
    {
        $config->setQuery(
            new ObjectType([
                'name'   => 'QueryType',
                'fields' => [
                    'echo' => [
                        'type' => new DateTimeType('Y-m-d H:ia'),
                        'args' => [
                            'date' => new DateTimeType('Y-m-d H:ia'),
                        ],
                        'resolve' => static function ($value, $args, $info) {
                            if (isset($args['date'])) {
                                return $args['date'];
                            }
                        },
                    ],
                ],
            ])
        );

        $config->setMutation(
            new ObjectType([
                'name'   => 'MutationType',
                'fields' => [
                    'echo' => [
                        'type' => new DateTimeType('Y-m-d H:ia'),
                        'args' => [
                            'date' => new DateTimeType('Y-m-d H:ia'),
                        ],
                        'resolve' => static function ($value, $args, $info) {
                            if (isset($args['date'])) {
                                return $args['date'];
                            }
                        },
                    ],
                ],
            ])
        );
    }
}
