<?php

namespace BlogTest;

/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/28/16 11:32 PM
*/


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
        return resolvePost();
    }

    public function build(TypeConfigInterface $config)
    {
        $config->addField('title', new NonNullType(new StringType()))
               ->addField('summary', new StringType());
//        $config->addArgument('id', new IdType());
    }

    public function getName()
    {
        return "Post";
    }

}

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);
$rootQueryType->getConfig()
              ->addField('latestPost', new PostType())
//              ->addField('post', new PostType())
;
