<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 2:02 AM
*/

namespace Youshido\Tests;
require_once __DIR__ . '/../vendor/autoload.php';


use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\DataProvider\UserType;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{


    public function testProcessor()
    {
        $schema = new Schema();
        $schema->addQuery(
            new ObjectType(
                [
                    'name'    => 'latest',
                    'fields'  => [
                        'id'   => ['type' => 'int'],
                        'name' => ['type' => 'string']
                    ],
                    'resolve' => function () {
                        return [
                            'id'   => 1,
                            'name' => 'Alex'
                        ];
                    }
                ]));

        $schema->addQuery(new UserType());

        $validator = new ResolveValidator();
        $processor = new Processor($validator);

        $processor->setSchema($schema);

        $processor->processQuery('{ latest { name } }');
        print_r($processor->getResponseData());

        $processor->processQuery('{ user(id:1) { id, name } }');
        print_r($processor->getResponseData());

    }

}
