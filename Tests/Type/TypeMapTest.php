<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/4/15 12:34 AM
*/

namespace Youshido\Tests\Type;


use Youshido\GraphQL\Type\TypeMap;

class TypeMapTest extends \PHPUnit_Framework_TestCase
{

    public function testScalarTypesSet()
    {
        $this->assertEquals($this->getScalarTypes(), TypeMap::getScalarTypes());
    }

    public function testScalarTypeCheck()
    {
        foreach ($this->getScalarTypes() as $type) {
            $this->assertTrue(TypeMap::isInputType($type));
        }
    }

    public function testScalarTypeObjectCreation()
    {
        foreach ($this->getScalarTypes() as $type) {
            $object = TypeMap::getScalarTypeObject($type);
            $this->assertEquals($object->getKind(), TypeMap::KIND_SCALAR);
        }
    }

    public function testScalarsAsInput()
    {
        foreach ($this->getScalarTypes() as $type) {
            $this->assertTrue(TypeMap::isInputType($type));
        }
    }

    private function getScalarTypes()
    {
        return [
            TypeMap::TYPE_INT,
            TypeMap::TYPE_FLOAT,
            TypeMap::TYPE_STRING,
            TypeMap::TYPE_BOOLEAN,
            TypeMap::TYPE_ID,
            TypeMap::TYPE_DATETIME,
            TypeMap::TYPE_DATE,
            TypeMap::TYPE_TIMESTAMP,
            TypeMap::TYPE_DATETIMETZ,
        ];
    }
}
