<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\StarWars;


use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\StarWars\Schema\Schema;

class StarWarsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $query
     * @param $validResult
     * @param $variables
     *
     * @dataProvider dataProvider
     */
    public function testSchema($query, $validResult, $variables)
    {
        $processor = new Processor(new ResolveValidator());
        $processor->setSchema(new Schema());

        $processor->processRequest($query, $variables);

        $responseData = $processor->getResponseData();
        $this->assertEquals($responseData, $validResult);
    }

    public function testInvalidVariableType()
    {
        $processor = new Processor(new ResolveValidator());
        $processor->setSchema(new Schema());

        $processor->processRequest(
            'query($someId: Int){
                human(id: $someId) {
                    name
                }
            }'
        );

        $data = $processor->getResponseData();
        $this->assertEquals($data, [
            'errors' => [
                [
                    'message' => 'Invalid variable "someId" type, allowed type is "Id"'
                ]
            ]
        ]);
    }


    public function dataProvider()
    {
        return [
            [
                'query CheckTypeOfLuke {
                  hero(episode: EMPIRE) {
                    __typename,
                    name
                  }
                }',
                [
                    'data' => [
                        'hero' => [
                            '__typename' => 'Human',
                            'name'       => 'Luke Skywalker'
                        ],
                    ]
                ],
                []
            ],
            [
                'query {
                  hero {
                    name
                  }
              }',
                ['data' => [
                    'hero' => [
                        'name' => 'R2-D2'
                    ]
                ]],
                []
            ],
            [
                'query {
                  hero {
                    id,
                    name,
                    friends {
                      name
                    }
                  }
                }',
                ['data' => [
                    'hero' => [
                        'id'      => '2001',
                        'name'    => 'R2-D2',
                        'friends' => [
                            [
                                'name' => 'Luke Skywalker',
                            ],
                            [
                                'name' => 'Han Solo',
                            ],
                            [
                                'name' => 'Leia Organa',
                            ],
                        ]
                    ]
                ]],
                []
            ],
            [
                '{
                  hero {
                    name,
                    friends {
                      name,
                      appearsIn,
                      friends {
                        name
                      }
                    }
                  }
                }',
                [
                    'data' => [
                        'hero' => [
                            'name'    => 'R2-D2',
                            'friends' => [
                                [
                                    'name'      => 'Luke Skywalker',
                                    'appearsIn' => ['NEWHOPE', 'EMPIRE', 'JEDI',],
                                    'friends'   => [
                                        ['name' => 'Han Solo',],
                                        ['name' => 'Leia Organa',],
                                        ['name' => 'C-3PO',],
                                        ['name' => 'R2-D2',],
                                    ],
                                ],
                                [
                                    'name'      => 'Han Solo',
                                    'appearsIn' => ['NEWHOPE', 'EMPIRE', 'JEDI'],
                                    'friends'   => [
                                        ['name' => 'Luke Skywalker',],
                                        ['name' => 'Leia Organa'],
                                        ['name' => 'R2-D2',],
                                    ]
                                ],
                                [
                                    'name'      => 'Leia Organa',
                                    'appearsIn' => ['NEWHOPE', 'EMPIRE', 'JEDI'],
                                    'friends'   =>
                                        [
                                            ['name' => 'Luke Skywalker',],
                                            ['name' => 'Han Solo',],
                                            ['name' => 'C-3PO',],
                                            ['name' => 'R2-D2',],
                                        ],
                                ],
                            ],
                        ]
                    ]
                ],
                []
            ],
            [
                '{
                  human(id: "1000") {
                    name
                  }
                }',
                [
                    'data' => [
                        'human' => [
                            'name' => 'Luke Skywalker'
                        ]
                    ]
                ],
                []
            ],
            [
                'query($someId: Id){
                  human(id: $someId) {
                    name
                  }
                }',
                [
                    'data' => [
                        'human' => [
                            'name' => 'Luke Skywalker'
                        ]
                    ]
                ],
                [
                    'someId' => '1000'
                ]
            ]
        ];
    }
}
