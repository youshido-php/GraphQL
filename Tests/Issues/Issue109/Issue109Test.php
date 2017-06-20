<?php

namespace Youshido\Tests\Issues\Issue109;

use Youshido\GraphQL\Execution\Processor;

class Issue109Test extends \PHPUnit_Framework_TestCase
{

    public function testInternalVariableArgument()
    {
        $schema    = new Issue109Schema();
        $processor = new Processor($schema);
        $response  = $processor->processPayload('
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
                'commentId' => 100
            ])->getResponseData();
        $this->assertEquals(['data' => ['latestPost' => [
            'id'       => 1,
            'comments' => [
                ['comment_id' => 100]
            ]
        ]]], $response);
    }
}
