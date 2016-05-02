<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/30/16 1:32 PM
*/

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
        return [
            "title"   => "Interesting approach",
            "summary" => "This new GraphQL library for PHP works really well",
            "status"  => 0
        ];
    }

    public function build(TypeConfigInterface $config)
    {
        $config->addField('title', new NonNullType(new StringType()))
               ->addField('summary', new NonNullType(new StringType()))
//               ->addField('status', new NonNullType(new PostStatus()))
        ;
        $config->addArgument('id', new IdType());
    }

//    public function getInterfaces()
//    {
//        return [new SummaryInterface()];
//    }


    public function getName()
    {
        return "Post";
    }

}
