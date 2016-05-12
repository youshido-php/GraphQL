<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/4/15 12:34 AM
*/

namespace Youshido\Tests\Type;


use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class TypeMapTest extends \PHPUnit_Framework_TestCase
{

    public function testScalarTypesSet()
    {
        $this->assertEquals($this->getScalarTypes(), TypeFactory::getScalarTypesNames());
    }

    public function testScalarTypeCheck()
    {
        foreach ($this->getScalarTypes() as $type) {
            $this->assertTrue(TypeService::isInputType($type));
        }
    }

    public function testScalarTypeObjectCreation()
    {
        foreach ($this->getScalarTypes() as $type) {
            $object = TypeFactory::getScalarType($type);
            $this->assertEquals($object->getKind(), TypeMap::KIND_SCALAR);
        }
    }

    public function testScalarsAsInput()
    {
        foreach ($this->getScalarTypes() as $type) {
            $this->assertTrue(TypeService::isInputType($type));
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
