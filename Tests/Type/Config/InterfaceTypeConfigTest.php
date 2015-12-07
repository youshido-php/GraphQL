<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/6/15 12:49 AM
*/

namespace Youshido\Tests\Type\Config;

use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\Schema\CharacterInterface;

class InterfaceTypeConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $characterInterface = new CharacterInterface();
        $this->assertEquals('Character', $characterInterface->getName());
        $this->assertEquals(TypeMap::KIND_INTERFACE, $characterInterface->getKind());

        $fields = $characterInterface->getConfig()->getFields();
        $this->assertArrayHasKey('id', $fields);

        $this->assertEquals(TypeMap::TYPE_ID, $fields['id']->getType()->getName());
        $this->assertEquals(TypeMap::KIND_SCALAR, $fields['id']->getType()->getKind());
    }

}
