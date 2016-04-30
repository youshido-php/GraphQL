<?php

namespace BlogTest;

/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/28/16 11:32 PM
 */


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractEnumType;
use Youshido\GraphQL\Type\Object\AbstractInterfaceType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostStatus extends AbstractEnumType
{
    public function getValues()
    {
        return [
            [
                'value' => '0',
                'name'  => 'DRAFT',
            ],
            [
                'value' => '1',
                'name'  => 'PUBLISHED',
            ]
        ];
    }

    public function getName()
    {
        return "PostStatus";
    }

}

class PostType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
        return resolvePost();
    }

    public function build(TypeConfigInterface $config)
    {
        $config->addField('title', new NonNullType(new StringType()))
               ->addField('summary', new StringType())
               ->addField('status', new NonNullType(new PostStatus()))
//               ->addField('status', new PostStatus())
        ;
        $config->addArgument('id', new IdType());
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
              ->addField('post', new PostType());
