<?php

namespace Youshido\Tests\Issues\Issue220;

use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\Tests\DataProvider\TestResolveInfo;

class Issue220Test extends TestCase
{

    public function testValueNotFoundInResolveScalarType()
    {
        $fieldWithResolve = new Field([
            'name' => 'scalarField',
            'type' => new StringType(),
        ]);

        $resolveInfo = TestResolveInfo::createTestResolveInfo($fieldWithResolve);

        $this->assertEquals(null, $fieldWithResolve->resolve([], [], $resolveInfo));
    }

    public function testValueNotFoundInResolveObjectType()
    {
        $fieldWithResolve = new Field([
            'name' => 'scalarField',
            'type' => new ArticleType(),
        ]);

        $resolveInfo = TestResolveInfo::createTestResolveInfo($fieldWithResolve);

        $this->assertEquals(null, $fieldWithResolve->resolve([], [], $resolveInfo));
    }

    public function testValueFoundInResolve()
    {
        $fieldWithResolve = new Field([
            'name' => 'scalarField',
            'type' => new StringType(),
        ]);

        $resolveInfo = TestResolveInfo::createTestResolveInfo($fieldWithResolve);

        $this->assertEquals('foo', $fieldWithResolve->resolve(['scalarField' => 'foo'], [], $resolveInfo));
    }
}

class ArticleType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'title' => new StringType(),
        ]);
    }
}
