<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\StarWars\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class HumanType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
        $humans = StarWarsData::humans();

        return isset($humans[$args['id']]) ? $humans[$args['id']] : null;
    }

    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('id', TypeMap::TYPE_ID)
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('friends', new ListType(new CharacterInterface()), [
                'resolve' => function ($droid) {
                    return StarWarsData::getFriends($droid);
                },
            ])
            ->addField('appearsIn', new ListType(new EpisodeEnum()))
            ->addField('homePlanet', TypeMap::TYPE_STRING);

        $config
            ->addArgument('id', TypeMap::TYPE_ID);
    }

    public function getInterfaces()
    {
        return [new CharacterInterface()];
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return 'Human';
    }
}
