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

class DroidType extends HumanType
{

    /**
     * @return String type name
     */
    public function getName()
    {
        return 'Droid';
    }

    protected function build(TypeConfigInterface $config)
    {
        parent::build($config);

        $config->getField('friends')->getConfig()->set('resolve', function($droid){
            return StarWarsData::getFriends($droid);
        });

        $config
            ->addField('primaryFunction', TypeMap::TYPE_STRING);
    }

    public function resolve($value = null, $args = [])
    {
        $droids = StarWarsData::droids();

        return isset($droids[$args['id']]) ? $droids[$args['id']] : null;
    }


}