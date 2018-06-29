<?php

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Directive\Directive;
use Youshido\GraphQL\Type\SchemaDirectivesList;

class SchemaDirectivesListTest extends \PHPUnit_Framework_TestCase
{
    public function testCanAddASingleDirective()
    {
        $directiveList = new SchemaDirectivesList();
        $directiveList->addDirective(
            new Directive([
                'name' => 'testDirective'
            ])
        );
        $this->assertTrue($directiveList->isDirectiveNameRegistered('testDirective'));
    }

    public function testCanAddMultipleDirectives()
    {
        $directiveList = new SchemaDirectivesList();
        $directiveList->addDirectives([
            new Directive([
                'name' => 'testDirectiveOne'
            ]),
            new Directive([
                'name' => 'testDirectiveTwo'
            ]),
        ]);
        $this->assertTrue($directiveList->isDirectiveNameRegistered('testDirectiveOne'));
        $this->assertTrue($directiveList->isDirectiveNameRegistered('testDirectiveTwo'));
    }

    public function testItThrowsExceptionWhenAddingInvalidDirectives()
    {
        $this->setExpectedException(\Exception::class, "addDirectives accept only array of directives");
        $directiveList = new SchemaDirectivesList();
        $directiveList->addDirectives("foobar");
    }

}