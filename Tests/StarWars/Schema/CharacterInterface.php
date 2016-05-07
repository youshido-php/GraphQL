<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/6/15 11:15 PM
*/

namespace Youshido\Tests\StarWars\Schema;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractInterfaceType;
use Youshido\GraphQL\Type\TypeMap;

class CharacterInterface extends AbstractInterfaceType
{
    public function build($config)
    {
        $config
            ->addField('id', TypeMap::TYPE_ID, ['required' => true])
            ->addField('name', TypeMap::TYPE_STRING, ['required' => true])
            ->addField('friends', new ListType(new CharacterInterface()), [
                'resolve' => function ($value) {
                    return $value['friends'];
                }
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

        $id = isset($object['id']) ? $object['id'] : $object;

        if (isset($humans[$id])) {
            return new HumanType();
        }

        if (isset($droids[$id])) {
            return new DroidType();
        }

        return null;
    }

}
