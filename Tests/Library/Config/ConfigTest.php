<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 10:41 PM
*/

namespace Youshido\Library;

require_once __DIR__ . '/../../DataProvider/TestConfig.php';
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeService;
use Youshido\Tests\DataProvider\TestConfig;

class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testEmptyParams()
    {
        new TestConfig([]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidParams()
    {
        new TestConfig(['id' => 1]);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidMethod()
    {
        $config = new TestConfig(['name' => 'test']);
        $config->doSomethingStrange();
    }

    public function testMethods()
    {
        $name = 'Test';
        $rules = [
            'name' => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'resolve' => ['type' => TypeService::TYPE_FUNCTION, 'final' => true],
        ];

        $config = new TestConfig(['name' => $name]);
        $this->assertEquals($config->getName(), $name);
        $this->assertEquals($config->get('name'), $name);
        $this->assertEquals($config->get('non existing key'), null);
        $this->assertEquals($config->set('name', 'StrangeName'), $config);
        $this->assertEquals($config->get('name'), 'StrangeName');
        $this->assertEquals($config->get('non existing', 'default'), 'default');
        $this->assertEquals($config->isName(), 'StrangeName');
        $this->assertEquals($config->setName('StrangeName 2'), $config);

        $this->assertEquals($config->getRules(), $rules);
        $this->assertEquals($config->getContextRules(), $rules);
        $this->assertTrue($config->isValid());
        $this->assertNull($config->getResolveFunction());

        $object = new ObjectType(['name' => 'TestObject', 'fields' => ['id' => new IntType()]]);

        $this->setExpectedException('Youshido\GraphQL\Validator\Exception\ConfigurationException');
        new TestConfig(['name' => $name . 'final'], $object, true);

        $finalConfig = new TestConfig(['name' => $name . 'final', 'resolve' => function() { return []; }], $object, true);
        $this->assertEquals($finalConfig->getType(), null);

        $rules['resolve']['required'] = true;
        $this->assertEquals($finalConfig->getContextRules(), $rules);

        $this->assertNotNull($finalConfig->getResolveFunction());

    }

}
