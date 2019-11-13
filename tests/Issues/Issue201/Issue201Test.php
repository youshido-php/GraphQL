<?php

namespace Youshido\Tests\Issues\Issue201;

use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;

class Issue201Test extends TestCase
{

    /**
     * @throws \Youshido\GraphQL\Exception\ConfigurationException
     * @expectedException \Youshido\GraphQL\Exception\ConfigurationException
     * @expectedExceptionMessage Type "user" was defined more than once
     */
    public function testExceptionOnDuplicateTypeName()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => [
                        'type' => new StringType(),
                    ],
                ],
            ]),
        ]);

        $schema->getQueryType()->addFields([
            'user' => new StringType(),
        ]);

        $schemaValidator = new SchemaValidator();
        $schemaValidator->validate($schema);
    }
}
