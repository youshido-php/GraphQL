<?php
namespace Youshido\Tests\Issues\Issue171;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class Issue171Schema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addField(
            'plan',
            [
                'type' => new PlanType(),
            ]
        );
    }
}

class PlanType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addField('kpi_status', [
            'type' => new KpiStatusType(),
        ]);
    }
}

class KpiStatusType extends AbstractEnumType
{
    public function getValues()
    {
        return [
            [
                'name'              => 'BAD',
                'value'             => 'Bad',
            ],
            [
                'name'              => 'GOOD',
                'value'             => 'Good',
            ],
            [
                'name'              => 'WARNING',
                'value'             => 'Warning',
            ]
        ];
    }
}