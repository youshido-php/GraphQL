<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 12:44 AM
*/

namespace Youshido\Tests\DataProvider;
require_once __DIR__ . '/../../vendor/autoload.php';

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class UserType extends AbstractObjectType
{

    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('id', 'int')
            ->addField('name', 'string');

        $config
            ->addArgument('id', 'int', [
                'required' => true
            ]);
    }


    public function resolve($value = null, $args = [])
    {
        return [
            'id'   => 1,
            'name' => 'John'
        ];
    }

    public function getName()
    {
        return "user";
    }

}