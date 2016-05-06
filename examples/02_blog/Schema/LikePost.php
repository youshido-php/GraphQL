<?php
/**
 * LikePost.php
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class LikePost extends AbstractObjectType
{

    /**
     * @param null  $value
     * @param array $args
     * @param PostType  $type
     * @return mixed
     */
    public function resolve($value = null, $args = [], $type = null)
    {
        return $type->resolve($value, $args);
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
