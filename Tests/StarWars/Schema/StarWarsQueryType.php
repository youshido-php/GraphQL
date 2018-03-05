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

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;

class StarWarsQueryType extends AbstractObjectType
{
    /**
     * @return string type name
     */
    public function getName()
    {
        return 'Query';
    }

    public function build($config): void
    {
        $config
            ->addField('hero', [
                'type' => new CharacterInterface(),
                'args' => [
                    'episode' => ['type' => new EpisodeEnum()],
                ],
                'resolve' => static function ($root, $args) {
                    return StarWarsData::getHero($args['episode'] ?? null);
                },
            ])
            ->addField(new Field([
                'name' => 'human',
                'type' => new HumanType(),
                'args' => [
                    'id' => new IdType(),
                ],
                'resolve' => static function ($value = null, $args = []) {
                    $humans = StarWarsData::humans();

                    return $humans[$args['id']] ?? null;
                },
            ]))
            ->addField(new Field([
                'name' => 'droid',
                'type' => new DroidType(),
                'args' => [
                    'id' => new IdType(),
                ],
                'resolve' => static function ($value = null, $args = []) {
                    $droids = StarWarsData::droids();

                    return $droids[$args['id']] ?? null;
                },
            ]));
    }
}
