<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/23/17 11:08 PM
 */

namespace Examples\BookStore\Schema\Type;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class BookType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'id'     => new IdType(),
            'title'  => new StringType(),
            'year'   => new IntType(),
            'isbn'   => new StringType(),
            'author' => new AuthorType(),
        ]);
    }

}