<?php

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

/**
 * Class TestSchema
 */
class TestSchema extends AbstractSchema
{
    private $testStatusValue = 0;

    public function build(SchemaConfig $config)
    {
        $config->setQuery(new ObjectType([
            'name'   => 'RootQuery',
            'fields' => [
                'me'     => [
                    'type'    => new TestObjectType(),
                    'resolve' => function ($value, $args, ResolveInfoInterface $info) {
                        return $info->getReturnType()->getData();
                    },
                ],
                'status' => [
                    'type'    => new TestEnumType(),
                    'resolve' => function () {
                        return $this->testStatusValue;
                    },
                ],
            ],
        ]));

        $config->setMutation(new ObjectType([
            'name'   => 'RootMutation',
            'fields' => [
                'updateStatus' => [
                    'type'    => new TestEnumType(),
                    'resolve' => function () {
                        return $this->testStatusValue;
                    },
                    'args'    => [
                        'newStatus' => new TestEnumType(),
                        'list'      => new ListType(new IntType()),
                    ],
                ],
            ],
        ]));
    }
}
