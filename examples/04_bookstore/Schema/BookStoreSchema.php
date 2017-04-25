<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/23/17 10:43 PM
 */

namespace Examples\BookStore\Schema;


use Examples\BookStore\DataProvider;
use Examples\BookStore\Schema\Field\Book\RecentBooksField;
use Examples\BookStore\Schema\Field\CategoriesField;
use Examples\BookStore\Schema\Type\AuthorType;
use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;

class BookStoreSchema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addFields([
            'authors' => [
                'type'    => new ListType(new AuthorType()),
                'resolve' => function () {
                    return DataProvider::getAuthors();
                }
            ],
            new RecentBooksField(),
            new CategoriesField(),
        ]);
    }

}