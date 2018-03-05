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
/**
 * This file is a part of GraphQL project.
 */

namespace Youshido\GraphQL\Relay\Type;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PageInfoType extends AbstractObjectType
{
    public function build($config): void
    {
        $config->addFields([
            'hasNextPage' => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating forwards, are there more items?',
            ],
            'hasPreviousPage' => [
                'type'        => new NonNullType(new BooleanType()),
                'description' => 'When paginating backwards, are there more items?',
            ],
            'startCursor' => [
                'type'        => new StringType(),
                'description' => 'When paginating backwards, the cursor to continue.',
            ],
            'endCursor' => [
                'type'        => new StringType(),
                'description' => 'When paginating forwards, the cursor to continue.',
            ],
        ]);
    }

    public function getDescription()
    {
        return 'Information about pagination in a connection.';
    }
}
