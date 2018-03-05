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

use Youshido\GraphQL\Execution\Processor;

class Issue109Test extends \PHPUnit_Framework_TestCase
{
    public function testInternalVariableArgument(): void
    {
        $schema    = new Issue109Schema();
        $processor = new Processor($schema);
        $response  = $processor->processPayload(
            '
query ($postId: Int, $commentId: Int) { 
    latestPost(id: $postId) { 
        id(comment_id: $commentId),
        comments(comment_id: $commentId) {
            comment_id
        } 
    } 
}',
            [
                'postId'    => 1,
                'commentId' => 100,
            ]
        )->getResponseData();
        $this->assertEquals(['data' => ['latestPost' => [
            'id'       => 1,
            'comments' => [
                ['comment_id' => 100],
            ],
        ]]], $response);
    }
}
