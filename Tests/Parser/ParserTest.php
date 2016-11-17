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
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;
use Youshido\GraphQL\Parser\Location;
use Youshido\GraphQL\Parser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyParser()
    {
        $parser = new Parser();

        $this->assertEquals([
            'queries'            => [],
            'mutations'          => [],
            'fragments'          => [],
            'fragmentReferences' => [],
            'variables'          => [],
            'variableReferences' => [],
        ], $parser->parse());
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\Parser\SyntaxErrorException
     */
    public function testInvalidSelection()
    {
        $parser = new Parser();
        $data = $parser->parse('
        {
            test {
                id
                image {
                    
                }
            }
        }
        ');
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
            'queries'            => [
                new Query('authors', null,
                    [
                        new Argument('category', new Literal('#2', new Location(1,1)), new Location(1,1))
                    ],
                    [
                        new Field('_id', null, [], new Location(1,1)),
                    ], new Location(1,1))
            ],
            'mutations'          => [],
            'fragments'          => [],
            'fragmentReferences' => [],
            'variables'          => [],
            'variableReferences' => []
        ]);
    }


    /**
     * @param $query string
     *
     * @dataProvider wrongQueriesProvider
     * @expectedException Youshido\GraphQL\Exception\Parser\SyntaxErrorException
     */
    public function testWrongQueries($query)
    {
        $parser = new Parser();

        $parser->parse($query);
    }

    public function testQueryWithNoFields()
    {
        $parser = new Parser();
        $data   = $parser->parse('{ name }');
        $this->assertEquals([
            'queries'            => [
                new Query('name', '', [], [], new Location(1,1))
            ],
            'mutations'          => [],
            'fragments'          => [],
            'fragmentReferences' => [],
            'variables'          => [],
            'variableReferences' => [],
        ], $data);
    }

    public function testQueryWithFields()
    {
        $parser = new Parser();
        $data   = $parser->parse('{ post, user { name } }');
        $this->assertEquals([
            'queries'            => [
                new Query('post', null, [], [], new Location(1,1)),
                new Query('user', null, [], [
                    new Field('name', null, [], new Location(1,1))
                ], new Location(1,1))
            ],
            'mutations'          => [],
            'fragments'          => [],
            'fragmentReferences' => [],
            'variables'          => [],
            'variableReferences' => [],
        ], $data);
    }

    public function testFragmentWithFields()
    {
        $parser = new Parser();
        $data   = $parser->parse('
            fragment FullType on __Type {
                kind
                fields {
                    name
                }
            }');
        $this->assertEquals([
            'queries'            => [],
            'mutations'          => [],
            'fragments'          => [
                new Fragment('FullType', '__Type', [
                    new Field('kind', null, [], new Location(1,1)),
                    new Query('fields', null, [], [
                        new Field('name', null, [], new Location(1,1))
                    ], new Location(1,1))
                ], new Location(1,1))
            ],
            'fragmentReferences' => [],
            'variables'          => [],
            'variableReferences' => [],
        ], $data);
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

        $this->assertEquals([
            'queries'            => [
                new Query('__schema', null, [], [
                    new Query('queryType', null, [], [
                        new Field('name', null, [], new Location(1,1))
                    ], new Location(1,1)),
                    new Query('mutationType', null, [], [
                        new Field('name', null, [], new Location(1,1))
                    ], new Location(1,1)),
                    new Query('types', null, [], [
                        new FragmentReference('FullType', new Location(1,1))
                    ], new Location(1,1)),
                    new Query('directives', null, [], [
                        new Field('name', null, [], new Location(1,1)),
                        new Field('description', null, [], new Location(1,1)),
                        new Query('args', null, [], [
                            new FragmentReference('InputValue', new Location(1,1)),
                        ], new Location(1,1)),
                        new Field('onOperation', null, [], new Location(1,1)),
                        new Field('onFragment', null, [], new Location(1,1)),
                        new Field('onField', null, [], new Location(1,1)),
                    ], new Location(1,1)),
                ], new Location(1,1))
            ],
            'mutations'          => [],
            'fragments'          => [
                new Fragment('FullType', '__Type', [
                    new Field('kind', null, [], new Location(1,1)),
                    new Field('name', null, [], new Location(1,1)),
                    new Field('description', null, [], new Location(1,1)),
                    new Query('fields', null, [], [
                        new Field('name', null, [], new Location(1,1)),
                        new Field('description', null, [], new Location(1,1)),
                        new Query('args', null, [], [
                            new FragmentReference('InputValue', new Location(1,1)),
                        ], new Location(1,1)),
                        new Query('type', null, [], [
                            new FragmentReference('TypeRef', new Location(1,1)),
                        ], new Location(1,1)),
                        new Field('isDeprecated', null, [], new Location(1,1)),
                        new Field('deprecationReason', null, [], new Location(1,1)),
                    ], new Location(1,1)),
                    new Query('inputFields', null, [], [
                        new FragmentReference('InputValue', new Location(1,1)),
                    ], new Location(1,1)),
                    new Query('interfaces', null, [], [
                        new FragmentReference('TypeRef', new Location(1,1)),
                    ], new Location(1,1)),
                    new Query('enumValues', null, [], [
                        new Field('name', null, [], new Location(1,1)),
                        new Field('description', null, [], new Location(1,1)),

                        new Field('isDeprecated', null, [], new Location(1,1)),
                        new Field('deprecationReason', null, [], new Location(1,1)),
                    ], new Location(1,1)),
                    new Query('possibleTypes', null, [], [
                        new FragmentReference('TypeRef', new Location(1,1)),
                    ], new Location(1,1)),
                ], new Location(1,1)),
                new Fragment('InputValue', '__InputValue', [
                    new Field('name', null, [], new Location(1,1)),
                    new Field('description', null, [], new Location(1,1)),
                    new Query('type', null, [], [
                        new FragmentReference('TypeRef', new Location(1,1)),
                    ], new Location(1,1)),
                    new Field('defaultValue', null, [], new Location(1,1)),
                ], new Location(1,1)),
                new Fragment('TypeRef', '__Type', [
                    new Field('kind', null, [], new Location(1,1)),
                    new Field('name', null, [], new Location(1,1)),
                    new Query('ofType', null, [], [
                        new Field('kind', null, [], new Location(1,1)),
                        new Field('name', null, [], new Location(1,1)),
                        new Query('ofType', null, [], [
                            new Field('kind', null, [], new Location(1,1)),
                            new Field('name', null, [], new Location(1,1)),
                            new Query('ofType', null, [], [
                                new Field('kind', null, [], new Location(1,1)),
                                new Field('name', null, [], new Location(1,1)),
                            ], new Location(1,1)),
                        ], new Location(1,1)),
                    ], new Location(1,1)),
                ], new Location(1,1)),
            ],
            'fragmentReferences' => [
                new FragmentReference('FullType', new Location(1,1)),
                new FragmentReference('InputValue', new Location(1,1)),
                new FragmentReference('InputValue', new Location(1,1)),
                new FragmentReference('TypeRef', new Location(1,1)),
                new FragmentReference('InputValue', new Location(1,1)),
                new FragmentReference('TypeRef', new Location(1,1)),
                new FragmentReference('TypeRef', new Location(1,1)),
                new FragmentReference('TypeRef', new Location(1,1)),
            ],
            'variables'          => [],
            'variableReferences' => []
        ], $data);
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
        $parser          = new Parser();
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
            'queries'            => [
                new Query('test', 'test', [],
                    [
                        new Field('name', null, [], new Location(1,1)),
                        new TypedFragmentReference('UnionType', [new Field('unionName', null, [], new Location(1,1))], new Location(1,1))
                    ], new Location(1,1))
            ],
            'mutations'          => [],
            'fragments'          => [],
            'fragmentReferences' => [],
            'variables'          => [],
            'variableReferences' => []
        ]);
    }

    public function mutationProvider()
    {
        return [
            [
                'query ($variable: Int){ query ( teas: $variable ) { alias: name } }',
                [
                    'queries'            => [
                        new Query('query', null,
                            [
                                new Argument('teas', new VariableReference('variable', (new Variable('variable', 'Int', false, false, new Location(1,1)))->setUsed(true), new Location(1,1)), new Location(1,1))
                            ],
                            [
                                new Field('name', 'alias', [], new Location(1,1))
                            ], new Location(1,1))
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [
                        (new Variable('variable', 'Int', false, false, new Location(1,1)))->setUsed(true)
                    ],
                    'variableReferences' => [
                        new VariableReference('variable', (new Variable('variable', 'Int', false, false, new Location(1,1)))->setUsed(true), new Location(1,1))
                    ]
                ]
            ],
            [
                '{ query { alias: name } }',
                [
                    'queries'            => [
                        new Query('query', null, [], [new Field('name', 'alias', [], new Location(1,1))], new Location(1,1))
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                'mutation { createUser ( email: "test@test.com", active: true ) { id } }',
                [
                    'queries'            => [],
                    'mutations'          => [
                        new Mutation(
                            'createUser',
                            null,
                            [
                                new Argument('email', new Literal('test@test.com', new Location(1,1)), new Location(1,1)),
                                new Argument('active', new Literal(true, new Location(1,1)), new Location(1,1)),
                            ],
                            [
                                new Field('id', null, [], new Location(1,1))
                            ],
                            new Location(1,1)
                        )
                    ],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                'mutation { test : createUser (id: 4) }',
                [
                    'queries'            => [],
                    'mutations'          => [
                        new Mutation(
                            'createUser',
                            'test',
                            [
                                new Argument('id', new Literal(4, new Location(1,1)), new Location(1,1)),
                            ],
                            [],
                            new Location(1,1)
                        )
                    ],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ]
        ];
    }

    /**
     * @dataProvider queryProvider
     */
    public function testParser($query, $structure)
    {
        $parser          = new Parser();
        $parsedStructure = $parser->parse($query);

        $this->assertEquals($structure, $parsedStructure);
    }


    public function queryProvider()
    {
        return [
            [
                '{ test (id: -5) { id } } ',
                [
                    'queries'            => [
                        new Query('test', null, [
                            new Argument('id', new Literal(-5, new Location(1,1)), new Location(1,1))
                        ], [
                            new Field('id', null, [], new Location(1,1)),
                        ], new Location(1,1))
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                "{ test (id: -5) \r\n { id } } ",
                [
                    'queries'            => [
                        new Query('test', null, [
                            new Argument('id', new Literal(-5, new Location(1,1)), new Location(1,1))
                        ], [
                            new Field('id', null, [], new Location(1,1)),
                        ], new Location(1,1))
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
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
                    'queries'            => [
                        new Query('hero', null, [
                            new Argument('episode', new Literal('EMPIRE', new Location(1,1)), new Location(1,1))
                        ], [
                            new Field('__typename', null, [], new Location(1,1)),
                            new Field('name', null, [], new Location(1,1)),
                        ], new Location(1,1))
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                '{ test { __typename, id } }',
                [
                    'queries'            => [
                        new Query('test', null, [], [
                            new Field('__typename', null, [], new Location(1,1)),
                            new Field('id', null, [], new Location(1,1)),
                        ], new Location(1,1))
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                '{}',
                [
                    'queries'            => [],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                'query test {}',
                [
                    'queries'            => [],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                'query {}',
                [
                    'queries'            => [],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                'mutation setName { setUserName }',
                [
                    'queries'            => [],
                    'mutations'          => [new Mutation('setUserName', null, [], [], new Location(1,1))],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                '{ test { ...userDataFragment } } fragment userDataFragment on User { id, name, email }',
                [
                    'queries'            => [
                        new Query('test', null, [], [new FragmentReference('userDataFragment', new Location(1,1))], new Location(1,1))
                    ],
                    'mutations'          => [],
                    'fragments'          => [
                        new Fragment('userDataFragment', 'User', [
                            new Field('id', null, [], new Location(1,1)),
                            new Field('name', null, [], new Location(1,1)),
                            new Field('email', null, [], new Location(1,1))
                        ], new Location(1,1))
                    ],
                    'fragmentReferences' => [
                        new FragmentReference('userDataFragment', new Location(1,1))
                    ],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                '{ user (id: 10, name: "max", float: 123.123 ) { id, name } }',
                [
                    'queries'            => [
                        new Query(
                            'user',
                            null,
                            [
                                new Argument('id', new Literal('10', new Location(1,1)), new Location(1,1)),
                                new Argument('name', new Literal('max', new Location(1,1)), new Location(1,1)),
                                new Argument('float', new Literal('123.123', new Location(1,1)), new Location(1,1))
                            ],
                            [
                                new Field('id', null, [], new Location(1,1)),
                                new Field('name', null, [], new Location(1,1))
                            ],
                            new Location(1,1)
                        )
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                '{ allUsers : users ( id: [ 1, 2, 3] ) { id } }',
                [
                    'queries'            => [
                        new Query(
                            'users',
                            'allUsers',
                            [
                                new Argument('id', new InputList([1, 2, 3], new Location(1,1)), new Location(1,1))
                            ],
                            [
                                new Field('id', null, [], new Location(1,1))
                            ],
                            new Location(1,1)
                        )
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                '{ allUsers : users ( id: [ 1, "2", true, null] ) { id } }',
                [
                    'queries'            => [
                        new Query(
                            'users',
                            'allUsers',
                            [
                                new Argument('id', new InputList([1, "2", true, null], new Location(1,1)), new Location(1,1))
                            ],
                            [
                                new Field('id', null, [], new Location(1,1))
                            ],
                            new Location(1,1)
                        )
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ],
            [
                '{ allUsers : users ( object: { "a": 123, "d": "asd",  "b" : [ 1, 2, 4 ], "c": { "a" : 123, "b":  "asd" } } ) { id } }',
                [
                    'queries'            => [
                        new Query(
                            'users',
                            'allUsers',
                            [
                                new Argument('object', new InputObject([
                                    'a' => 123,
                                    'd' => 'asd',
                                    'b' => [1, 2, 4],
                                    'c' => new InputObject([
                                        'a' => 123,
                                        'b' => 'asd'
                                    ], new Location(1,1))
                                ], new Location(1,1)), new Location(1,1))
                            ],
                            [
                                new Field('id', null, [], new Location(1,1))
                            ],
                            new Location(1,1)
                        )
                    ],
                    'mutations'          => [],
                    'fragments'          => [],
                    'fragmentReferences' => [],
                    'variables'          => [],
                    'variableReferences' => []
                ]
            ]
        ];
    }

    public function testVariablesInQuery()
    {
        $parser = new Parser();

        $data = $parser->parse('
            query StarWarsAppHomeRoute($names_0:[String]!, $query: String) {
              factions(names:$names_0, test: $query) {
                id,
                ...F2
              }
            }
            fragment F0 on Ship {
              id,
              name
            }
            fragment F1 on Faction {
              id,
              factionId
            }
            fragment F2 on Faction {
              id,
              factionId,
              name,
              _shipsDRnzJ:ships(first:10) {
                edges {
                  node {
                    id,
                    ...F0
                  },
                  cursor
                },
                pageInfo {
                  hasNextPage,
                  hasPreviousPage
                }
              },
              ...F1
            }
        ');

        $this->assertArrayNotHasKey('errors', $data);
    }

}
