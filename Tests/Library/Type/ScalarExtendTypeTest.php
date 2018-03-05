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
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 8/14/16 12:16 PM
 */

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\Tests\DataProvider\TestTimeType;

class ScalarExtendTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testType(): void
    {
        $reportType = new ObjectType([
            'name'   => 'Report',
            'fields' => [
                'time'  => new TestTimeType(),
                'title' => new StringType(),
            ],
        ]);
        $processor = new Processor(
            new Schema([
                'query' => new ObjectType([
                    'name'   => 'RootQueryType',
                    'fields' => [
                        'latestReport' => [
                            'type'    => $reportType,
                            'resolve' => static function () {
                                return [
                                    'title' => 'Accident #1',
                                    'time'  => '13:30:12',
                                ];
                            },
                        ],
                    ],
                ]),
            ])
        );

        $processor->processPayload('{ latestReport { title, time} }');
        $this->assertEquals(['data' => ['latestReport' => ['title' => 'Accident #1', 'time' => '13:30:12']]], $processor->getResponseData());
    }
}
