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

use Youshido\GraphQL\Type\TypeMap;

class DroidType extends HumanType
{
    /**
     * @return string type name
     */
    public function getName()
    {
        return 'Droid';
    }

    public function build($config): void
    {
        parent::build($config);

        $config->getField('friends')->getConfig()->set('resolve', static function ($droid) {
            return StarWarsData::getFriends($droid);
        });

        $config
            ->addField('primaryFunction', TypeMap::TYPE_STRING);
    }

    public function getInterfaces()
    {
        return [new CharacterInterface()];
    }
}
