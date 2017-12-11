<?php

namespace Youshido\Tests\Library\Execution\Container;

use Psr\Container\ContainerInterface;
use Youshido\GraphQL\Execution\Container\Container;

/**
 * Class ContainerTest
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Youshido\GraphQL\Execution\Container\Exception\NotFoundException
     */
    public function testNotFoundException()
    {
        $container = new Container();
        $container->set('id', 'test');

        $container->get('id2');
    }

    /**
     * @expectedExceptionMessage test
     * @expectedException Youshido\GraphQL\Execution\Container\Exception\ContainerException
     */
    public function testContainerException()
    {
        $container = new Container();
        $container->set('service', function () {
            throw new \RuntimeException('test');
        });

        $container->get('service');
    }

    public function testContainerWorking()
    {
        $container = new Container();

        $container->set('parameter', 'a1');
        $this->assertEquals('a1', $container->get('parameter'));
        $this->assertEquals(['parameter'], $container->keys());

        $container->set('service1', function () {
            $object           = new \stdClass();
            $object->property = 'a1';

            return $object;
        });

        $service           = new \stdClass();
        $service->property = 'a1';
        $actualService     = $container->get('service1');
        $this->assertEquals($service, $actualService);


        $actualService->propertyTwo = 'b2';
        $service->propertyTwo       = 'b2';
        $this->assertEquals($service, $container->get('service1'));

        $this->assertEquals(['parameter', 'service1'], $container->keys());

        $container->set('service2', new Invokable());
        /** @var Invokable $service2 */
        $service2 = $container->get('service2');

        $this->assertEquals('a', $service2->getA());

        $this->assertTrue($container->has('parameter'));
        $this->assertTrue($container->has('service1'));
        $this->assertTrue($container->has('service2'));
    }
}

class Invokable
{
    private $a;

    public function getA()
    {
        return $this->a;
    }

    public function __invoke()
    {
        $this->a = 'a';

        return $this;
    }
}
