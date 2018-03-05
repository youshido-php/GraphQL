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
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 12/6/15 11:15 PM
 */

namespace Youshido\Tests\StarWars\Schema;

use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CharacterInterface extends AbstractInterfaceType
{
    public function build($config): void
    {
        $config
            ->addField('id', new NonNullType(new IdType()))
            ->addField('name', new NonNullType(new StringType()))
            ->addField('friends', [
                'type'    => new ListType(new self()),
                'resolve' => static function ($value) {
                    return $value['friends'];
                },
            ])
            ->addField('appearsIn', new ListType(new EpisodeEnum()));
    }

    public function getDescription()
    {
        return 'A character in the Star Wars Trilogy';
    }

    public function getName()
    {
        return 'Character';
    }

    public function resolveType($object)
    {
        $humans = StarWarsData::humans();
        $droids = StarWarsData::droids();

        $id = $object['id'] ?? $object;

        if (isset($humans[$id])) {
            return new HumanType();
        }

        if (isset($droids[$id])) {
            return new DroidType();
        }
    }
}
