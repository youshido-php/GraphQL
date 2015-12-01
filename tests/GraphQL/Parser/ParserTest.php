<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\tests\GraphQL\Parser;


use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\InputList;
use Youshido\GraphQL\Parser\Ast\Literal;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provider
     */
    public function testParser($query, $structure)
    {
        $parser = new Parser();
        $parser->setSource($query);

        $parsedStructure = $parser->parse();

        $this->assertEquals($parsedStructure, $structure);
    }


    public function provider()
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
                '{ user (id: 10, name: "max" ) { id, name } }',
                [
                    'queries'   => [
                        new Query(
                            'user',
                            null,
                            [
                                new Argument('id', new Literal('10')),
                                new Argument('name', new Literal('max'))
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
            ]
        ];
    }

}