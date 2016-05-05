<?php
/**
 * BannerType.php
 */

namespace Examples\Blog\Schema;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class BannerType extends AbstractObjectType
{
    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('title', new StringType())
            ->addField('imageLink', new StringType());
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        return [
            'title' => 'Banner 1',
            'imageLink' => 'banner1.jpg'
        ];
    }


}
