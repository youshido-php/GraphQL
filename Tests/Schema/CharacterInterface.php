<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/6/15 11:15 PM
*/

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Type\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractInterfaceType;
use Youshido\GraphQL\Type\TypeMap;

class CharacterInterface extends AbstractInterfaceType
{
    protected function build(InterfaceTypeConfig $config)
    {
        $config
            ->addField('id', TypeMap::TYPE_ID, ['required' => true])
            ->addField('name', TypeMap::TYPE_STRING, ['required' => true])
            ->addField('friends', new ListType(['item' => new CharacterInterface()]), [
                'resolve' => function($value){
                    return $value['friends'];
                }
            ])
            ->addField('appearsIn', new ListType(['item' => new EpisodeEnum()]));
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

        if (isset($humans[$object])) {
            return $humans[$object];
        }
        if (isset($droids[$object])) {
            return $droids[$object];
        }

        return null;
    }

}