<?php

namespace Youshido\Tests\Issues\Issue210;

use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Type\TypeService;

class TypeServiceTest extends TestCase
{

    public function testGetPropertyValue()
    {
        $object = new DummyObjectWithTrickyGetters();

        $this->assertEquals('Foo', TypeService::getPropertyValue($object, 'issuer'));
        $this->assertEquals('something', TypeService::getPropertyValue($object, 'something'));
        $this->assertEquals('Bar', TypeService::getPropertyValue($object, 'issuerName'));
    }
}

class DummyObjectWithTrickyGetters
{
    public function getIssuer()
    {
        return 'Foo';
    }

    public function something()
    {
        return 'something';
    }

    public function issuerName()
    {
        return 'Bar';
    }
}
