<?php
/**
 * BlogSchema.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Type\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\StringType;

class BlogSchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addFields([
            'latestPost'           => new PostType(),
            'randomBanner'         => [
                'type'    => new BannerType(),
                'resolve' => function () {
                    return DataProvider::getBanner(rand(1, 10));
                }
            ],
            'pageContentUnion'     => [
                'type'    => new ListType(new ContentBlockUnion()),
                'resolve' => function () {
                    return [DataProvider::getPost(1), DataProvider::getBanner(1)];
                }
            ],
            'pageContentInterface' => [
                'type'    => new ListType(new ContentBlockInterface()),
                'resolve' => function () {
                    return [DataProvider::getPost(2), DataProvider::getBanner(3)];
                }
            ]
        ]);
        $config->getMutation()->addFields([
            'likePost'   => new LikePost(),
            'createPost' => [
                'type'   => new PostType(),
                'args' => [
                    'post'   => new PostInputType(),
                    'author' => new StringType()
                ],
                'resolve' => function($value, $args, $type) {
                    return DataProvider::getPost(10);
                }
            ]
        ]);
    }

}
