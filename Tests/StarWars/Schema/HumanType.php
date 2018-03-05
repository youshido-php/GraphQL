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

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;

class HumanType extends AbstractObjectType
{
    public function build($config): void
    {
        $config
            ->addField('id', new NonNullType(new IdType()))
            ->addField('name', new NonNullType(new StringType()))
            ->addField('friends', [
                'type'    => new ListType(new CharacterInterface()),
                'resolve' => static function ($droid) {
                    return StarWarsData::getFriends($droid);
                },
            ])
            ->addField('appearsIn', new ListType(new EpisodeEnum()))
            ->addField('homePlanet', TypeMap::TYPE_STRING);
    }

    public function getInterfaces()
    {
        return [new CharacterInterface()];
    }
}
