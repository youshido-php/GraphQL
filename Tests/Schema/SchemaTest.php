<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 10:11 PM
*/

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class SchemaTest extends \PHPUnit_Framework_TestCase
{

    public function testInlineSchema()
    {
        $queryType = new ObjectType([
            'name'   => 'RootQueryType',
            'fields' => [
                'currentTime' => [
                    'type'    => new StringType(),
                    'resolve' => function ($value, $args, $type) {
                        return 'May 5, 9:00am';
                    },
                    'args'    => [
                        'gmt' => [
                            'type' => new IntType(),
                            'default' => -5
                        ],
                    ],
                ]
            ]
        ]);
        /** it's probably wrong to not pass the default ARGS in the resolve */
        $this->assertEquals('May 5, 9:00am', $queryType->getField('currentTime')->resolve([]));


    }

}
