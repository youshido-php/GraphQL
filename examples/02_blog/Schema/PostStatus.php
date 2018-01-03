<?php

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;

/**
 * Class PostStatus
 */
class PostStatus extends AbstractEnumType
{
    /**
     * @param EnumTypeConfig $config
     */
    protected function build(EnumTypeConfig $config)
    {
        $config->setValues([
            [
                'value' => 0,
                'name'  => 'DRAFT',
            ],
            [
                'value' => 1,
                'name'  => 'PUBLISHED',
            ],
        ]);
    }
}
