<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/24/17 11:40 PM
 */

namespace Examples\BookStore\Schema\Type;

use Examples\BookStore\Schema\Field\CategoriesField;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CategoryType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'id'      => new IdType(),
            'title'   => new StringType(),
            'authors' => new ListType(new AuthorType()),
            new CategoriesField(),
        ]);
    }
}
