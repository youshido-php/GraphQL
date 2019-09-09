<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 3:59 PM 5/17/16
 */

namespace Youshido\Tests\Library\Relay;


use InvalidArgumentException;
use Youshido\GraphQL\Relay\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $global     = Node::toGlobalId('user', 1);
        $fromGlobal = Node::fromGlobalId($global);

        $this->assertEquals('user', $fromGlobal[0]);
        $this->assertEquals(1, $fromGlobal[1]);
    }

    public function malformedIdProvider()
    {
        return [
            [''],
            [base64_encode('I have no colon')],
            [null],
        ];
    }

    /**
     * @dataProvider malformedIdProvider
     */
    public function testFromGlobalIdThrowsExceptionIfGivenMalformedId($idToCheck)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Node::fromGlobalId($idToCheck);
    }
}
