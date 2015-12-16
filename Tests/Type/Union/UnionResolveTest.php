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

        $a = $processor->getResponseData();

        $this->assertEquals($processor->getResponseData(), $response);
    }

    public function dataProvider()
    {
        return [
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
            ]
        ];
    }

}