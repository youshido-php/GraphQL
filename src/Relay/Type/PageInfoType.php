<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2/23/17 4:19 PM
 */

namespace Youshido\GraphQL\Relay\Type;


use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PageInfoType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'hasNextPage'     => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating forwards, are there more items?'
            ],
            'hasPreviousPage' => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating backwards, are there more items?'
            ],
            'startCursor'     => [
                'type'        => new StringType(),
                'description' => 'When paginating backwards, the cursor to continue.'
            ],
            'endCursor'       => [
                'type'        => new StringType(),
                'description' => 'When paginating forwards, the cursor to continue.'
            ],
        ]);
    }

    public function getDescription()
    {
        return "Information about pagination in a connection.";
    }

}
