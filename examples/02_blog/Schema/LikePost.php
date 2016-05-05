<?php
/**
 * LikePost.php
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
        /**
         * You can do return $type->resolve($value, $args);
         */
        return $type->resolve($value, $args);
        return [
            "title"     => "Post title from the LikePost Mutation class",
            "summary"   => "This new GraphQL library for PHP works really well",
            "likeCount" => 3
        ];

    }

    public function getType()
    {
        return new PostType();
    }

    public function build(TypeConfigInterface $config)
    {
        $config->addArgument('id', new NonNullType(new IntType()));
    }

}
