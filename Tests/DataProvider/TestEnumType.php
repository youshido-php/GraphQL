<?php

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;

/**
 * Class TestEnumType
 */
class TestEnumType extends AbstractEnumType
{
    /**
     * @param EnumTypeConfig $config
     */
    protected function build(EnumTypeConfig $config)
    {
        $config->setValues([
            [
                'name'  => 'FINISHED',
                'value' => 1,
            ],
            [
                'name'  => 'NEW',
                'value' => 0,
            ]
        ]);
    }
}
