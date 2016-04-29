<?php
namespace BlogTest;
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 8:21 PM 4/28/16
 */


use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;

require_once __DIR__ . '/../../vendor/autoload.php';

$processor = new Processor(new ResolveValidator());
$processor->setSchema(new Schema([
    'query' => new ObjectType([
        'fields'    => [
            'post'  =>
        ]
    ])
]));
