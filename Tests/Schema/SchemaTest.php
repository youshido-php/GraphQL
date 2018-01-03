<?php

namespace Youshido\Tests\Schema;

use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Execution\TypeCollector;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\Tests\DataProvider\TestObjectType;
use Youshido\Tests\DataProvider\TestSchema;

/**
 * Class SchemaTest
 */
class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testStandaloneSchema()
    {
        $schema = new TestSchema();
        $this->assertTrue($schema->getQueryType()->hasFields());
        $this->assertTrue($schema->getMutationType()->hasFields());

        $this->assertEquals(1, count($schema->getMutationType()->getFields()));

        $schema->getMutationType()->addField('changeUser', ['type' => new TestObjectType(), 'resolve' => function () {
        }]);
        $this->assertEquals(2, count($schema->getMutationType()->getFields()));

    }

    public function testSchemaWithoutClosuresSerializable()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'randomInt' => [
                        'type'    => new NonNullType(new IntType()),
                        'resolve' => 'rand',
                    ],
                ],
            ]),
        ]);

        $serialized = serialize($schema);
        /** @var Schema $unserialized */
        $unserialized = unserialize($serialized);

        $this->assertTrue($unserialized->getQueryType()->hasFields());
        $this->assertNull($unserialized->getMutationType());
        $this->assertEquals(1, count($unserialized->getQueryType()->getFields()));
    }

    public function testCustomTypes()
    {
        $authorType    = new AuthorType();
        $userInterface = new UserInterface();

        $schema = new Schema([
            'types' => [$authorType],
            'query' => new ObjectType([
                'name'   => 'QueryType',
                'fields' => [
                    'user' => [
                        'type'    => $userInterface,
                        'resolve' => function () {
                            return [
                                'name' => 'Alex',
                            ];
                        },
                    ],
                ],
            ]),
        ]);

        TypeCollector::getInstance()->clear();
        $processor = new Processor(new ExecutionContext($schema));
        $processor->processPayload('{ user { name } }');
        $data = $processor->getResponseData();
        $this->assertEquals(['data' => ['user' => ['name' => 'Alex']]], $data);

        $processor->processPayload('{
                    __schema {
                        types {
                            name
                        }
                    }
                }');
        $data = $processor->getResponseData();
        $this->assertArraySubset([11 => ['name' => 'Author']], $data['data']['__schema']['types']);

        $processor->processPayload('{ user { name { } } }');
        $result = $processor->getResponseData();

        $this->assertEquals(['errors' => [[
                                              'message'   => 'Unexpected token "RBRACE"',
                                              'locations' => [
                                                  [
                                                      'line'   => 1,
                                                      'column' => 19,
                                                  ],
                                              ],
                                          ]]], $result);
        $processor->getExecutionContext()->clearErrors();

        $processor->processPayload('{ user { name { invalidSelection } } }');
        $result = $processor->getResponseData();

        $this->assertEquals(['errors' => [[
                                              'message'   => 'You can\'t specify fields for scalar type "String"',
                                              'locations' => [
                                                  [
                                                      'line'   => 1,
                                                      'column' => 10,
                                                  ],
                                              ],
                                          ]]], $result);
    }

}

class AuthorType extends AbstractObjectType
{

    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $this->addFields([
            'name' => new StringType(),
        ]);
    }

    public function getInterfaces()
    {
        return [new UserInterface()];
    }
}

class UserInterface extends AbstractInterfaceType
{

    /**
     * @param mixed $object
     *
     * @return AbstractType
     */
    public function resolveType($object)
    {
        return new AuthorType();
    }

    /**
     * @param InterfaceTypeConfig $config
     */
    public function build($config)
    {
        $config->addFields([
            'name' => new StringType(),
        ]);
    }
}
