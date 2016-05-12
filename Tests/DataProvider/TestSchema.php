<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/3/15 11:28 PM
*/

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Config\Schema\SchemaConfig;


class TestSchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
    }


    public function getName()
    {
        return 'TestSchema';
    }
}
