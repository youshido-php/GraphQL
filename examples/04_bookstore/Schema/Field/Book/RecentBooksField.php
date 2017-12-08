<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/23/17 11:09 PM
 */

namespace Examples\BookStore\Schema\Field\Book;


use Examples\BookStore\DataProvider;
use Examples\BookStore\Schema\Type\BookType;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Type\ListType\ListType;

class RecentBooksField extends AbstractField
{
    public function getType()
    {
        return new ListType(new BookType());
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return DataProvider::getBooks();
    }


}