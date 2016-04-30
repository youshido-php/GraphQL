<?php

namespace BlogTest;

/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/28/16 11:32 PM
 */


use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Type\Object\ObjectType;


require_once __DIR__ . '/../Schema/PostType.php';
require_once __DIR__ . '/../Schema/PostStatus.php';
require_once __DIR__ . '/../Schema/SummaryInterface.php';


$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);
$rootQueryType->getConfig()
              ->addField('latestPost', new PostType())
//              ->addField('post', new PostType())
;
