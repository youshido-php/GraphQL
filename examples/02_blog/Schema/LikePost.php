<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:27 PM 5/3/16
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractMutationObjectType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class LikePost extends AbstractObjectType
{
    public function resolve($value = null, $args = [], $type = null)
    {
//        return $type->resolve($value, $args);
        return [
            "title"     => "Post title from the LikePost Mutation class",
            "summary"   => "This new GraphQL library for PHP works really well",
            "likeCount" => 3
        ];

    }

    public function getOutputType()
    {
        return new PostType();
    }

    public function getType()
    {
        return new PostType();
    }

    public function build(TypeConfigInterface $config)
    {
        $config->addArgument('id', new NonNullType(new IntType()));
    }

    public function getName()
    {
        return 'likePost';
    }

}
