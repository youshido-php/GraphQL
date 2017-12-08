<?php

namespace Youshido\GraphQL\Relay\Type;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class PageInfoType
 */
class PageInfoType extends AbstractObjectType
{
    /**
     * @param \Youshido\GraphQL\Config\Object\ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config->addFields([
            'hasNextPage'     => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating forwards, are there more items?',
            ],
            'hasPreviousPage' => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating backwards, are there more items?',
            ],
            'startCursor'     => [
                'type'        => new StringType(),
                'description' => 'When paginating backwards, the cursor to continue.',
            ],
            'endCursor'       => [
                'type'        => new StringType(),
                'description' => 'When paginating forwards, the cursor to continue.',
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Information about pagination in a connection.';
    }
}
