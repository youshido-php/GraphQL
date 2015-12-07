<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests;


use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\Schema\Schema;

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

        $processor->processQuery($query, $variables);

        $this->assertEquals($processor->getResponseData(), $validResult);
    }


    public function dataProvider()
    {
        return [
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
                '{
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