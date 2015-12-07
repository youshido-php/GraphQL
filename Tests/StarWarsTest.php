<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests;


use Youshido\Tests\Schema\Schema;

class StarWarsTest extends \PHPUnit_Framework_TestCase
{

    public function testSchema()
    {
        $schema = new Schema();

        $config = $schema->getQueryType()->getConfig();

    }

}