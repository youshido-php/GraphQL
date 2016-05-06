<?php
/**
 * PostType.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;

class PostType extends AbstractObjectType
{
    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('oldTitle', new NonNullType(new StringType()), [
                'description'       => 'This field contains a post title',
                'isDeprecated'      => true,
                'deprecationReason' => 'field title is now deprecated',
                'args'              => [
                    'truncated' => new BooleanType()
                ],
                'resolve'           => function ($value, $args) {
                    return (!empty($args['truncated'])) ? explode(' ', $value)[0] . '...' : $value;
                }
            ])
            ->addField('title', new NonNullType(new StringType()))
            ->addField('status', new PostStatus())
            ->addField('summary', new StringType())
            ->addField('likeCount', new IntType());
    }

    public function getInterfaces()
    {
        return [new ContentBlockInterface()];
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        return DataProvider::getPost(empty($args['id']) ? 1 : $args['id']);
    }

}
