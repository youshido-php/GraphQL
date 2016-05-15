<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\StarWars\Schema;


use Youshido\GraphQL\Type\Object\AbstractObjectType;

class QueryType extends AbstractObjectType
{

    public function resolve($value = null, $args = [], $type = null)
    {
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return 'Query';
    }

    public function build($config)
    {
        $config
            ->addField('hero', [
                'type'    => new CharacterInterface(),
                'args'    => [
                    'episode' => ['type' => new EpisodeEnum()]
                ],
                'resolve' => function ($root, $args) {
                    return StarWarsData::getHero(isset($args['episode']) ? $args['episode'] : null);
                },
            ])
            ->addField('human', new HumanType())
            ->addField('droid', new DroidType());
    }
}