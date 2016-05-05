<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/4/16 10:10 PM
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
