<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\StarWars\Schema;


use Youshido\GraphQL\Type\Config\Schema\SchemaConfig;

class Schema extends \Youshido\GraphQL\Schema
{

    public function build(SchemaConfig $config)
    {
        $config->setQuery(new QueryType());
    }

}