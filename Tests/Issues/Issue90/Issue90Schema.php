<?php

namespace Youshido\Tests\Issues\Issue90;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue90Schema extends AbstractSchema
{

    public function build(SchemaConfig $config)
    {
        $config->setQuery(
            new ObjectType([
                'name'   => 'QueryType',
                'fields' => [
                    'echo' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'date' => new StringType(),
                        ],
                        'resolve' => function ($value, $args, $info) {

                            if (isset($args['date'])) {
                                return $args['date'];
                            }

                            return null;
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
                        'type'    => new StringType(),
                        'args'    => [
                            'date' => new StringType(),
                        ],
                        'resolve' => function ($value, $args, $info) {

                            if (isset($args['date'])) {
                                return $args['date'];
                            }

                            return null;
                        },
                    ],
                ],
            ])
        );
    }

}
