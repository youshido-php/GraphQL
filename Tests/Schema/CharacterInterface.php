<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/6/15 11:15 PM
*/

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Type\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\InterfaceType;
use Youshido\GraphQL\Type\TypeMap;

class CharacterInterface extends InterfaceType
{
    protected function build(TypeConfigInterface $config)
    {
        /** @var InterfaceTypeConfig $config */
//        $config->addField('id', TypeMap::TYPE_ID, ['description' => 'ID of the character'])
//            ->addField('name', TypeMap::TYPE_STRING, ['description' => 'The name of the character'])
//            ;
        $config->addFields([
                               'id'   => ['type'        => TypeMap::TYPE_ID,
                                          'required'    => true,
                                          'description' => 'ID of the character'],
                               'name' => ['type'        => TypeMap::TYPE_STRING,
                                          'description' => 'The name of the character'],
                               'friends'   => new ListType(['item' => new CharacterInterface()])
                           ]);
    }

    public function getDescription()
    {
        return 'A character in the Star Wars Trilogy';
    }

    public function getName()
    {
        return 'Character';
    }

}