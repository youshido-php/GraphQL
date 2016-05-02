<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/30/16 1:31 PM
*/

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Object\AbstractEnumType;

class PostStatus extends AbstractEnumType
{
    public function getValues()
    {
        return [
            [
                'value' => '0',
                'name'  => 'DRAFT',
            ],
            [
                'value' => '1',
                'name'  => 'PUBLISHED',
            ]
        ];
    }

    public function getName()
    {
        return "PostStatus";
    }

}