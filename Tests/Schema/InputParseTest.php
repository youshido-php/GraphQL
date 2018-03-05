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

namespace Youshido\Tests\Schema;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\DateTimeTzType;
use Youshido\GraphQL\Type\Scalar\StringType;

class InputParseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider queries
     *
     * @param $query
     * @param $expected
     */
    public function testDateInput($query, $expected): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'stringQuery' => [
                        'type' => new StringType(),
                        'args' => [
                            'from'   => new DateTimeType('Y-m-d H:i:s'),
                            'fromtz' => new DateTimeTzType(),
                        ],
                        'resolve' => static function ($source, $args) {
                            return \sprintf(
                                'Result with %s date and %s tz',
                                empty($args['from']) ? 'default' : $args['from']->format('Y-m-d H:i:s'),
                                empty($args['fromtz']) ? 'default' : $args['fromtz']->format('r')
                            );
                        },
                    ],
                ],
            ]),
        ]);

        $processor = new Processor($schema);
        $processor->processPayload($query);
        $result = $processor->getResponseData();

        $this->assertEquals($expected, $result);
    }

    public function queries()
    {
        return [
            [
                '{
                  stringQuery(fromtz: "Mon, 14 Nov 2016 04:48:13 +0000")
                }',
                [
                    'data' => [
                        'stringQuery' => 'Result with default date and Mon, 14 Nov 2016 04:48:13 +0000 tz',
                    ],
                ],
            ],
            [
                '{
                  stringQuery(from: "2016-10-30 06:10:22")
                }',
                [
                    'data' => [
                        'stringQuery' => 'Result with 2016-10-30 06:10:22 date and default tz',
                    ],
                ],
            ],
        ];
    }
}
