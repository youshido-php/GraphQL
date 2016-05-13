<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 10:11 PM
*/

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Type\Object\ObjectType;
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
                    'resolve' => function () {
                        return 'May 5, 9:00am';
                    }
                ]
            ]
        ]);
        $this->assertEquals('May 5, 9:00am', $queryType->getField('currentTime')->resolve([]));
    }

}
