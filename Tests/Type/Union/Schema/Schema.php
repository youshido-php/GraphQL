<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Type\Union\Schema;


use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Config\Schema\SchemaConfig;

class Schema extends AbstractSchema
{

    public function build(SchemaConfig $config)
    {
        $config->setQuery(new QueryType());
    }

}
