<?php

namespace Youshido\Tests\Issues\Issue99;

use Youshido\GraphQL\Execution\Processor;

/**
 * User: m-naw
 * Date: 2/02/17
 */
class Issue99Test extends \PHPUnit_Framework_TestCase
{
    const BUG_NOT_EXISTS_VALUE = 'bug not exists';
    const BUG_EXISTS_VALUE = 'bug exists';

    public function testQueryDateTimeTypeWithDateParameter()
    {
        $schema = new Issue99Schema();
        $processor = new Processor($schema);
        $processor->processPayload(sprintf("{ items{id, custom(argX: {x: \"%s\"}){ value } } }", self::BUG_NOT_EXISTS_VALUE));
        $res = $processor->getResponseData();

        self::assertTrue(isset($res['data']['items']));

        foreach($res['data']['items'] as $item) {
            self::assertTrue(isset($item['custom']['value']));
            self::assertEquals(self::BUG_NOT_EXISTS_VALUE, $item['custom']['value']);
        }
    }
}