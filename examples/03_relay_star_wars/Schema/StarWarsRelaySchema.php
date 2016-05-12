<?php

namespace Examples\StarWars;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Config\Schema\SchemaConfig;

class StarWarsRelaySchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $queryTypeConfig = $config->getQuery()->getConfig();

        Node::addNodeField($queryTypeConfig);

        $queryTypeConfig->addField('faction', new FactionType());
    }

}
