<?php
/**
 * ContentBlockUnion.php
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

class ContentBlockUnion extends AbstractUnionType
{
    public function getTypes()
    {
        return [new PostType(), new BannerType()];
    }

    public function resolveType($object, ResolveInfoInterface $resolveInfo)
    {
        return empty($object['id']) ? null : (strpos($object['id'], 'post') !== false ? new PostType() : new BannerType());
    }

}
