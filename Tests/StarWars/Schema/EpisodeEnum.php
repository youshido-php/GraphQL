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
 * Date: 07.12.15.
 */

namespace Youshido\Tests\StarWars\Schema;

use Youshido\GraphQL\Type\Enum\AbstractEnumType;

class EpisodeEnum extends AbstractEnumType
{
    public function getValues()
    {
        return [
            [
                'value'       => 4,
                'name'        => 'NEWHOPE',
                'description' => 'Released in 1977.',
            ],
            [
                'value'       => 5,
                'name'        => 'EMPIRE',
                'description' => 'Released in 1980.',
            ],
            [
                'value'       => 6,
                'name'        => 'JEDI',
                'description' => 'Released in 1983.',
            ],
        ];
    }

    /**
     * @return string type name
     */
    public function getName()
    {
        return 'Episode';
    }
}
