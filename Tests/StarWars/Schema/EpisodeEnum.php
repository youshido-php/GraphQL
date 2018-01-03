<?php

namespace Youshido\Tests\StarWars\Schema;

use Youshido\GraphQL\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;

/**
 * Class EpisodeEnum
 */
class EpisodeEnum extends AbstractEnumType
{
    /**
     * @return String type name
     */
    public function getName()
    {
        return 'Episode';
    }

    /**
     * @param EnumTypeConfig $config
     */
    protected function build(EnumTypeConfig $config)
    {
        $config->setValues([
            [
                'value'       => 4,
                'name'        => 'NEWHOPE',
                'description' => 'Released in 1977.'
            ],
            [
                'value'       => 5,
                'name'        => 'EMPIRE',
                'description' => 'Released in 1980.'
            ],
            [
                'value'       => 6,
                'name'        => 'JEDI',
                'description' => 'Released in 1983.'
            ],
        ]);
    }
}
