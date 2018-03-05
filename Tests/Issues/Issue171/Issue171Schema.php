<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace Youshido\Tests\Issues\Issue171;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class Issue171Schema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
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
    public function build($config): void
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
                'name'  => 'BAD',
                'value' => 'Bad',
            ],
            [
                'name'  => 'GOOD',
                'value' => 'Good',
            ],
            [
                'name'  => 'WARNING',
                'value' => 'Warning',
            ],
        ];
    }
}
