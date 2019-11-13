<?php

namespace Youshido\Tests\Issues\Issue116Test;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Union\UnionType;

class Issue151Test extends \PHPUnit_Framework_TestCase
{
    public function testInternalVariableArgument()
    {
        $type1 = new ObjectType([
            'name'   => 'Type1',
            'fields' => [
                'id'   => new IdType(),
                'name' => new StringType(),
            ],
        ]);
        $type2 = new ObjectType([
            'name'   => 'Type2',
            'fields' => [
                'id'    => new IdType(),
                'title' => new StringType(),
            ],
        ]);

        $unionType = new UnionType([
            'name'        => 'Union',
            'types'       => [$type1, $type2],
            'resolveType' => function ($value) use ($type1, $type2) {
                if (isset($value['name'])) {
                    return $type1;
                }

                return $type2;
            },
        ]);

        $schema    = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'list' => [
                        'type'    => new ListType($unionType),
                        'resolve' => function () {
                            return [
                                [
                                    'id'   => 1,
                                    'name' => 'name',
                                ],
                                [
                                    'id'    => 2,
                                    'title' => 'title',
                                ],
                            ];
                        },
                    ],
                ],
            ]),
        ]);
        $processor = new Processor($schema);
        $response  = $processor->processPayload('
{
    list {
        ...UnitFragment
    }
}

fragment UnitFragment on Union {
    __typename
    
    ... on Type1 {
        id
        name
    }
    ... on Type2 {
        id
        title
    }
}
        ')->getResponseData();

    }
}
