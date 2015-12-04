<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/2/15 11:36 PM
*/

namespace Youshido\Tests\DataProvider;
require_once __DIR__ . '/../../vendor/autoload.php';

use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\ListType;

class GeoSchema extends Schema
{

    public function buildTypes(TypeConfigInterface $config)
    {
        $config
            ->addField('users', new ListType(['item' => new UserType()]));
    }

    public function buildMutations(TypeConfigInterface $config)
    {
    }

    public function getName()
    {
        return 'GeoSchema';
    }

}