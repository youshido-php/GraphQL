<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Parser;

use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputList;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputObject;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;
use Youshido\GraphQL\Parser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyParser()
    {
        $parser = new Parser();

        $this->assertEquals(['queries' => [], 'mutations' => [], 'fragments' => []], $parser->parse());
    }

    /**
     * @expectedException Youshido\GraphQL\Parser\Exception\VariableTypeNotDefined
     */
    public function testTypeNotDefinedException()
    {
        $parser = new Parser();
        $parser->parse('query getZuckProfile($devicePicSize: Int, $second: Int) {
                  user(id: 4) {
                    profilePic(size: $devicePicSize, test: $second, a: $c) {
                        url
                    }
                  }
                }');

    }

    /**
     * @expectedException Youshido\GraphQL\Parser\Exception\UnusedVariableException
     */
    public function testTypeUnusedVariableException()
    {
        $parser = new Parser();
        $parser->parse('query getZuckProfile($devicePicSize: Int, $second: Int) {
                  user(id: 4) {
                    profilePic(size: $devicePicSize) {
                        url
                    }
                  }
                }');
    }

    /**
     * @expectedException Youshido\GraphQL\Parser\Exception\DuplicationVariableException
     */
    public function testDuplicationVariableException()
    {
        $parser = new Parser();
        $parser->parse('query getZuckProfile($devicePicSize: Int, $second: Int, $second: Int) {
                  user(id: 4) {
                    profilePic(size: $devicePicSize, test: $second) {
                        url
                    }
                  }
                }');
    }

    public function testComments()
    {
        $query = '
            # asdasd "asdasdasd"
            # comment line 2

            query {
              authors (category: "#2") { #asda asd
                _id
              }
            }
        ';


        $parser = new Parser();

        $this->assertEquals($parser->parse($query), [
            'queries'   => [
                new Query('authors', null,
                    [
                        new Argument('category', new Literal('#2'))
                    ],
                    [
                        new Field('_id', null),
                    ])
            ],
            'mutations' => [],
            'fragments' => []
        ]);
    }


    /**
     * @param $query string
     *
     * @dataProvider wrongQueriesProvider
     * @expectedException Youshido\GraphQL\Parser\Exception\SyntaxErrorException
     */
    public function testWrongQueries($query)
    {
        $parser = new Parser();

        $parser->parse($query);
    }

    public function testInspectionQuery()
    {
        $parser = new Parser();

        $data = $parser->parse('
            query IntrospectionQuery {
                __schema {
                    queryType { name }
                    mutationType { name }
                    types {
                        ...FullType
                    }
                    directives {
                        name
                        description
                        args {
                            ...InputValue
                        }
                        onOperation
                        onFragment
                        onField
                    }
                }
            }

            fragment FullType on __Type {
                kind
                name
                description
                fields {
                    name
                    description
                    args {
                        ...InputValue
                    }
                    type {
                        ...TypeRef
                    }
                    isDeprecated
                    deprecationReason
                }
                inputFields {
                    ...InputValue
                }
                interfaces {
                    ...TypeRef
                }
                enumValues {
                    name
                    description
                    isDeprecated
                    deprecationReason
                }
                possibleTypes {
                    ...TypeRef
                }
            }

            fragment InputValue on __InputValue {
                name
                description
                type { ...TypeRef }
                defaultValue
            }

            fragment TypeRef on __Type {
                kind
                name
                ofType {
                    kind
                    name
                    ofType {
                        kind
                        name
                        ofType {
                            kind
                            name
                        }
                    }
                }
            }
        ');

        $this->assertTrue(is_array($data));
    }

    public function wrongQueriesProvider()
    {
        return [
            ['{ test { id,, asd } }'],
            ['{ test { id,, } }'],
            ['{ test (a: "asd", b: <basd>) { id }'],
            ['{ test (asd: [..., asd]) { id } }'],
            ['{ test (asd: { "a": 4, "m": null, "asd": false  "b": 5, "c" : { a }}) { id } }'],
            ['asdasd'],
            ['mutation { test(asd: ... ){ ...,asd, asd } }'],
            ['mutation { test( asd: $,as ){ ...,asd, asd } }'],
            ['mutation { test{ . test on Test { id } } }'],
            ['mutation { test( a: "asdd'],
            ['mutation { test( a: { "asd": 12 12'],
            ['mutation { test( a: { "asd": 12'],
        ];
    }

    /**
     * @dataProvider mutationProvider
     */
    public function testMutations($query, $structure)
    {
        $parser = new Parser();

        $parsedStructure = $parser->parse($query);

        $this->assertEquals($parsedStructure, $structure);
    }

    public function testTypedFragment()
    {
        $parser = new Parser();
        $parsedStructure = $parser->parse('
            {
                test: test {
                    name,
                    ... on UnionType {
                        unionName
                    }
                }
            }
        ');

        $this->assertEquals($parsedStructure, [
            'queries'   => [
                new Query('test', 'test', [],
                    [
                        new Field('name', null),
                        new TypedFragmentReference('UnionType', [
                            new Field('unionName')
                        ])
                    ])
            ],
            'mutations' => [],
            'fragments' => []
        ]);
    }

    public function mutationProvider()
    {
        return [
            [
                'query ($variable: Int){ query ( teas: $variable ) { alias: name } }',
                [
                    'queries'   => [
                        new Query('query', null,
                            [
                                new Argument('teas', new Variable('variable', 'Int'))
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
                    'queries'   => [
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
        $parsedStructure = $parser->parse($query);

        $this->assertEquals($parsedStructure, $structure);
    }


    public function queryProvider()
    {
        return [
            [
                '{ test (id: -5) { id } } ',
                [
                    'queries'   => [
                        new Query('test', null, [
                            new Argument('id', new Literal(-5))
                        ], [
                            new Field('id'),
                        ])
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                "{ test (id: -5) \r\n { id } } ",
                [
                    'queries'   => [
                        new Query('test', null, [
                            new Argument('id', new Literal(-5))
                        ], [
                            new Field('id'),
                        ])
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                'query CheckTypeOfLuke {
                  hero(episode: EMPIRE) {
                    __typename,
                    name
                  }
                }',
                [
                    'queries'   => [
                        new Query('hero', null, [
                            new Argument('episode', new Literal('EMPIRE'))
                        ], [
                            new Field('__typename'),
                            new Field('name'),
                        ])
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                '{ test { __typename, id } }',
                [
                    'queries'   => [
                        new Query('test', null, [], [
                            new Field('__typename'),
                            new Field('id'),
                        ])
                    ],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                '{}',
                [
                    'queries'   => [],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                'query test {}',
                [
                    'queries'   => [],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                'query {}',
                [
                    'queries'   => [],
                    'mutations' => [],
                    'fragments' => []
                ]
            ],
            [
                'mutation setName { setUserName }',
                [
                    'queries'   => [],
                    'mutations' => [new Mutation('setUserName')],
                    'fragments' => []
                ]
            ],
            [
                '{ test { ...userDataFragment } } fragment userDataFragment on User { id, name, email }',
                [
                    'queries'   => [
                        new Query('test', null, [], [new FragmentReference('userDataFragment')])
                    ],
                    'mutations' => [],
                    'fragments' => [
                        new Fragment('userDataFragment', 'User', [
                            new Field('id'),
                            new Field('name'),
                            new Field('email')
                        ])
                    ]
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
