<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Type\Union;


use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\Type\Union\Schema\Schema;

class UnionResolveTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $query
     * @param $response
     *
     * @dataProvider dataProvider
     */
    public function testResolve($query, $response)
    {
        $processor = new Processor(new ResolveValidator());
        $processor->setSchema(new Schema());

        $processor->processQuery($query);

        $this->assertEquals($processor->getResponseData(), $response);
    }

    public function dataProvider()
    {
        return [
            [
                '{
                    list : unionList {
                        name,
                        ... on FirstType {
                            secondName
                        }
                    }
                }',
                [
                    'data' => [
                        'list' => [
                            ['name' => 'object 1'],
                            ['name' => 'object 2', 'secondName' => 'object second name 2'],
                            ['name' => 'object 3'],
                            ['name' => 'object 4', 'secondName' => 'object second name 4']
                        ]
                    ]
                ]
            ],
            [
                '{
                    oneUnion {
                        name,
                        ... on FirstType {
                            secondName
                        }
                    }
                }',
                [
                    'data' => [
                        'oneUnion' => [
                            'name' => 'object one',
                            'secondName' => 'second name'
                        ]
                    ]
                ]
            ],
            [
                '{
                    list : unionList {
                        name
                    }
                }',
                [
                    'data' => [
                        'list' => [
                            ['name' => 'object 1'],
                            ['name' => 'object 2'],
                            ['name' => 'object 3'],
                            ['name' => 'object 4']
                        ]
                    ]
                ]
            ],
            [
                '{
                    oneUnion {
                        name
                    }
                }',
                [
                    'data' => [
                        'oneUnion' => [
                            'name' => 'object one'
                        ]
                    ]
                ]
            ],
        ];
    }

}