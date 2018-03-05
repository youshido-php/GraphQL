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
/**
 * Date: 07.12.15.
 */

namespace Youshido\Tests\StarWars;

use Youshido\GraphQL\Execution\Processor;
use Youshido\Tests\StarWars\Schema\StarWarsSchema;

class StarWarsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $query
     * @param $validResult
     * @param $variables
     *
     * @dataProvider dataProvider
     */
    public function testSchema($query, $validResult, $variables): void
    {
        $processor = new Processor(new StarWarsSchema());

        $processor->processPayload($query, $variables);

        $responseData = $processor->getResponseData();
        $this->assertEquals($validResult, $responseData);
    }

    public function testInvalidVariableType(): void
    {
        $processor = new Processor(new StarWarsSchema());

        $processor->processPayload(
            'query($someId: Int){
                human(id: $someId) {
                    name
                }
            }',
            ['someId' => 1]
        );

        $data = $processor->getResponseData();
        $this->assertEquals($data, [
            'data'   => ['human' => null],
            'errors' => [
                [
                    'message'   => 'Invalid variable "someId" type, allowed type is "ID"',
                    'locations' => [
                        [
                            'line'   => 1,
                            'column' => 7,
                        ],
                    ],
                ],
            ],
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
                            'name'       => 'Luke Skywalker',
                        ],
                    ],
                ],
                [],
            ],
            [
                'query {
                  hero {
                    name
                  }
              }',
                ['data' => [
                    'hero' => [
                        'name' => 'R2-D2',
                    ],
                ]],
                [],
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
                        ],
                    ],
                ]],
                [],
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
                                    'appearsIn' => ['NEWHOPE', 'EMPIRE', 'JEDI'],
                                    'friends'   => [
                                        ['name' => 'Han Solo'],
                                        ['name' => 'Leia Organa'],
                                        ['name' => 'C-3PO'],
                                        ['name' => 'R2-D2'],
                                    ],
                                ],
                                [
                                    'name'      => 'Han Solo',
                                    'appearsIn' => ['NEWHOPE', 'EMPIRE', 'JEDI'],
                                    'friends'   => [
                                        ['name' => 'Luke Skywalker'],
                                        ['name' => 'Leia Organa'],
                                        ['name' => 'R2-D2'],
                                    ],
                                ],
                                [
                                    'name'      => 'Leia Organa',
                                    'appearsIn' => ['NEWHOPE', 'EMPIRE', 'JEDI'],
                                    'friends'   => [
                                            ['name' => 'Luke Skywalker'],
                                            ['name' => 'Han Solo'],
                                            ['name' => 'C-3PO'],
                                            ['name' => 'R2-D2'],
                                        ],
                                ],
                            ],
                        ],
                    ],
                ],
                [],
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
                            'name' => 'Luke Skywalker',
                        ],
                    ],
                ],
                [],
            ],
            [
                'query($someId: ID){
                  human(id: $someId) {
                    name
                  }
                }',
                [
                    'data' => [
                        'human' => [
                            'name' => 'Luke Skywalker',
                        ],
                    ],
                ],
                [
                    'someId' => '1000',
                ],
            ],
        ];
    }
}
