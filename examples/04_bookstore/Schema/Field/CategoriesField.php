<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/24/17 11:42 PM
 */

namespace Examples\BookStore\Schema\Field;


use Examples\BookStore\Schema\Type\CategoryType;
use Youshido\GraphQL\Execution\DeferredResolver;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Type\ListType\ListType;

class CategoriesField extends AbstractField
{
    public function getType()
    {
        return new ListType(new CategoryType());
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return new DeferredResolver(function () use ($value) {
            $id = empty($value['id']) ? "1" : $value['id'] . ".1";

            return [
                'id'       => $id,
                'title'    => 'Category ' . $id,
                'authors'  => [
                    [
                        'id'        => 'author1',
                        'firstName' => 'John',
                        'lastName'  => 'Doe',
                    ],
                ],
                'children' => [],
            ];
        });
    }
}