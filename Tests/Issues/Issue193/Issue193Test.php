<?php

namespace Youshido\Tests\Issues\Issue193;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue193Test extends \PHPUnit_Framework_TestCase
{
    public function testResolvedInterfacesShouldBeRegistered()
    {
        $schema    = new Issue193Schema();
        $processor = new Processor($schema);

        $processor->processPayload($this->getIntrospectionQuery(), []);
        $resp = $processor->getResponseData();

        $typeNames = array_map(function ($type) {
            return $type['name'];
        }, $resp['data']['__schema']['types']);

        // Check that all types are discovered
        $this->assertContains('ContentBlockInterface', $typeNames);
        $this->assertContains('Post', $typeNames);
        $this->assertContains('Undiscovered', $typeNames);

        // Check that possibleTypes for interfaces are discovered
        $contentBlockInterfaceType = null;

        foreach ($resp['data']['__schema']['types'] as $type) {
            if ($type['name'] === 'ContentBlockInterface') {
                $contentBlockInterfaceType = $type;
                break;
            }
        }

        $this->assertNotNull($contentBlockInterfaceType);
        $this->assertEquals([
            ['name' => 'Post'],
            ['name' => 'Undiscovered'],
        ], $contentBlockInterfaceType['possibleTypes']);
    }

    private function getIntrospectionQuery()
    {
        return <<<TEXT
query IntrospectionQuery {
    __schema {
        types {
            kind
          	name
          	possibleTypes {
          	    name
          	}
        }
    }
}
TEXT;
    }
}

class Issue193Schema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addField(
            'post',
            [
                'type' => new PostType(),
            ]
        );
    }
}

class PostType extends AbstractObjectType
{

    public function build($config)
    {
        $config->applyInterface(new ContentBlockInterface());
        $config->addFields([
            'likesCount' => new IntType(),
        ]);
    }

    public function getInterfaces()
    {
        return [new ContentBlockInterface()];
    }
}

class UndiscoveredType extends AbstractObjectType
{
    public function build($config)
    {
        $config->applyInterface(new ContentBlockInterface());
    }
}

class ContentBlockInterface extends AbstractInterfaceType
{
    public function build($config)
    {
        $config->addField('title', new NonNullType(new StringType()));
        $config->addField('summary', new StringType());
    }

    public function resolveType($object)
    {
        if (isset($object['title'])) {
            return new PostType();
        }

        return new UndiscoveredType();
    }

    public function getImplementations()
    {
        return [
            new PostType(),
            new UndiscoveredType(),
        ];
    }
}
