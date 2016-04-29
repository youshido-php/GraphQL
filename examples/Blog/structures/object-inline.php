<?php

namespace BlogTest;

/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/28/16 11:32 PM
*/


use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);
$rootQueryType->getConfig()->addField('latestPost',
    new ObjectType([
        'name'   => 'Post',
        'fields' => [
            'title'   => new StringType(),
            'summary' => new StringType(),
        ],
        'args'   => [
            'id' => new IdType(),
        ],
    ]),
    [
        'resolve' => function () {
            return resolvePost();
        }
    ]);