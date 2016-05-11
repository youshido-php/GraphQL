<?php

namespace Examples\StarWars;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Type\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;

class StarWarsRelaySchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addFields([
            'factions' => new ListType(),
        ]);
    }

}
