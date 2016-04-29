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
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\Type\Union\Schema\QueryType;

require_once __DIR__ . '/../../vendor/autoload.php';

$processor = new Processor(new ResolveValidator());
//$rootQueryType = new ObjectType([
//                                    'name'   => 'RootQueryType',
//                                    'fields' => [
//                                        'latestPost' => new ObjectType([
//                                                                           'name'    => 'latestPost',
//                                                                           'fields'  => [
//                                                                               'title' => new StringType()
//                                                                           ],
//                                                                           'resolve' => function () {
//                                                                               die('resolve');
//                                                                           }
//                                                                       ])
//                                    ]
//                                ]);

$rootQueryType = new ObjectType([
                                    'name' => 'RootQueryType',

                                ]);
$rootQueryType->getConfig()->addField('latestPost', new ObjectType([
                                                                       'name'   => 'latestPost',
                                                                       'fields' => [
                                                                           'title' => new StringType(),
                                                                           'summary' => new StringType(),
                                                                       ],

                                                                   ]), [
                                          'resolve' => function () {
                                              return [
                                                  "title" => "Interesting approach",
                                                  "summary" => "This new GraphQL library for PHP works really well",
                                              ];
                                          }
                                      ]);

$processor->setSchema(new Schema([
                                     'query' => $rootQueryType
                                 ]));
$res = $processor->processQuery('{ latestPost { title, summary } }')->getResponseData();
var_dump($res);
echo "\n\n";
die();