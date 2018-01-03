<?php

namespace Youshido\Tests\Library\Utilities;

use Youshido\GraphQL\Execution\PropertyAccessor\DefaultPropertyAccessor;
use Youshido\Tests\DataProvider\TestObjectType;

/**
 * Class TypeUtilitiesTest
 */
class TypeUtilitiesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPropertyValue()
    {
        $arrayData = (new TestObjectType())->getData();

        $propertyAccessor = new DefaultPropertyAccessor();

        $this->assertEquals('John', $propertyAccessor->getValue($arrayData, 'name'));
        $this->assertEquals('John', $propertyAccessor->getValue((object) $arrayData, 'name'));
    }
}
