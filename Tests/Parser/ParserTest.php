<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\tests\GraphQL\Parser;

use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Value\InputList;
use Youshido\GraphQL\Parser\Value\InputObject;
use Youshido\GraphQL\Parser\Value\Literal;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Parser\Value\Variable;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider mutationProvider
     */
    public function testMutations($query, $structure)
    {
        $parser = new Parser();
        $parser->setSource($query);

        $parsedStructure = $parser->parse();

        $this->assertEquals($parsedStructure, $structure);
    }

    public function mutationProvider()
    {
        return [
            [
                '{ query ( teas: $variable ) { alias: name } }',
                [
                    'queries' => [
                        new Query('query', null,
                            [
                                new Argument('teas', new Variable('variable'))
                            ],
                            [
                                new Field('name', 'alias')
                            ])
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                '{ query { alias: name } }',
                [
                    'queries' => [
                        new Query('query', null, [], [new Field('name', 'alias')])
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                'mutation { createUser ( email: "test@test.com", active: true ) { id } }',
                [
                    'queries'   => [],
                    'mutations' => [
                        new Mutation(
                            'createUser',
                            null,
                            [
                                new Argument('email', new Literal('test@test.com')),
                                new Argument('active', new Literal(true)),
                            ],
                            [
                                new Field('id')
                            ]
                        )
                    ],
                    'fragments' => []
                ]
            ],
            [
                'mutation { test : createUser (id: 4) }',
                [
                    'queries'   => [],
                    'mutations' => [
                        new Mutation(
                            'createUser',
                            'test',
                            [
                                new Argument('id', new Literal(4)),
                            ],
                            []
                        )
                    ],
                    'fragments' => []
                ]
            ]
        ];
    }

    /**
     * @dataProvider queryProvider
     */
    public function testParser($query, $structure)
    {
        $parser = new Parser();
        $parser->setSource($query);

        $parsedStructure = $parser->parse();

        $this->assertEquals($parsedStructure, $structure);
    }


    public function queryProvider()
    {
        return [
            [
                '{}',
                [
                    'queries'   => [],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                '{ user (id: 10, name: "max", float: 123.123 ) { id, name } }',
                [
                    'queries'   => [
                        new Query(
                            'user',
                            null,
                            [
                                new Argument('id', new Literal('10')),
                                new Argument('name', new Literal('max')),
                                new Argument('float', new Literal('123.123'))
                            ],
                            [
                                new Field('id'),
                                new Field('name')
                            ]
                        )
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                '{ allUsers : users ( id: [ 1, 2, 3] ) { id } }',
                [
                    'queries'   => [
                        new Query(
                            'users',
                            'allUsers',
                            [
                                new Argument('id', new InputList([1, 2, 3]))
                            ],
                            [
                                new Field('id')
                            ]
                        )
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                '{ allUsers : users ( id: [ 1, "2", true, null] ) { id } }',
                [
                    'queries'   => [
                        new Query(
                            'users',
                            'allUsers',
                            [
                                new Argument('id', new InputList([1, "2", true, null]))
                            ],
                            [
                                new Field('id')
                            ]
                        )
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                '{ allUsers : users ( object: { "a": 123, "d": "asd",  "b" : [ 1, 2, 4 ], "c": { "a" : 123, "b":  "asd" } } ) { id } }',
                [
                    'queries'   => [
                        new Query(
                            'users',
                            'allUsers',
                            [
                                new Argument('object', new InputObject([
                                    'a' => 123,
                                    'd' => 'asd',
                                    'b' => [1, 2, 4],
                                    'c' => [
                                        'a' => 123,
                                        'b' => 'asd'
                                    ]
                                ]))
                            ],
                            [
                                new Field('id')
                            ]
                        )
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ]
        ];
    }

}