<?php
/**
 * PostType.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostType extends AbstractObjectType
{
    /**
     * @param \Youshido\GraphQL\Config\Object\ObjectTypeConfig $config
     * @throws \Youshido\GraphQL\Exception\ConfigurationException
     */
    public function build($config)
    {
        $config
            ->addField('oldTitle', [
                'type'              => new NonNullType(new StringType()),
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
            ->addField(
                'title', [
                    'type'  => new NonNullType(new StringType()),
                    'args'  => [
                        'truncated' => new BooleanType()
                    ],
                ])
            ->addField('status', new PostStatus())
            ->addField('summary', new StringType())
            ->addField('likeCount', new IntType());
    }

    public function getOne($id)
    {
        return DataProvider::getPost($id);
    }

    public function getInterfaces()
    {
        return [new ContentBlockInterface()];
    }
}
