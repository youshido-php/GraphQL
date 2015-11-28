<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 2:02 AM
*/

namespace Youshido\Tests;


use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Validator\Validator;
use Youshido\GraphqQL\Processor;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    public function testProcessor()
    {
        $rootQuery = new ObjectType(
            [
                'name'   => 'RootQuery',
                'fields' => [
                    'users' => new ObjectType([])
                ]
            ]);

        $validator = new Validator();
        $processor = new Processor($validator);
        $processor->processQuery();

    }

}
