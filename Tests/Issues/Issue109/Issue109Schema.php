<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace Youshido\Tests\Issues\Issue109;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class Issue109Schema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
    {
        $config->setQuery(
            new ObjectType([
                'name'   => 'RootQueryType',
                'fields' => [
                    'latestPost' => [
                        'type' => new ObjectType([
                            'name'   => 'Post',
                            'fields' => [
                                'id' => [
                                    'type' => new IntType(),
                                    'args' => [
                                        'comment_id' => new IntType(),
                                    ],
                                ],
                                'comments' => [
                                    'type' => new ListType(new ObjectType([
                                        'name'   => 'Comment',
                                        'fields' => [
                                            'comment_id' => new IntType(),
                                        ],
                                    ])),
                                    'args' => [
                                        'comment_id' => new IntType(),
                                    ],
                                ],
                            ],
                        ]),
                        'resolve' => static function ($source, array $args, ResolveInfo $info) {
                            $internalArgs = [
                                'comment_id' => 200,
                            ];

                            if ($field = $info->getFieldAST('comments')->hasArguments()) {
                                $internalArgs['comment_id'] = $info->getFieldAST('comments')->getArgumentValue('comment_id');
                            }

                            return [
                                'id'       => 1,
                                'title'    => 'New approach in API has been revealed',
                                'summary'  => 'In two words - GraphQL Rocks!',
                                'comments' => [
                                    [
                                        'comment_id' => $internalArgs['comment_id'],
                                    ],
                                ],
                            ];
                        },
                        'args' => [
                            'id' => new IntType(),
                        ],
                    ],
                ],
            ])
        );
    }
}
