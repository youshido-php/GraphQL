<?php
/**
 * LikePost.php
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class LikePost extends AbstractObjectType
{

    /**
     * @param null  $value
     * @param array $args
     * @param PostType $type
     * @return mixed
     */
    public function resolve($value = null, $args = [], $type = null)
    {
        return $type->getOne($args['id']);
    }

    public function getType()
    {
        return new PostType();
    }

    public function build($config)
    {
        $config->addField('id', new NonNullType(new IntType()));
    }

}
