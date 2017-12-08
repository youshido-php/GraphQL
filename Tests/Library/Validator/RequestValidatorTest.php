<?php
/**
 * Date: 27.10.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Library\Validator;


use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Location;
use Youshido\GraphQL\Parser\ParseResult;
use Youshido\GraphQL\Validator\RequestValidator\RequestValidator;
use Youshido\GraphQL\Validator\RequestValidator\Validator\FragmentsValidator;
use Youshido\GraphQL\Validator\RequestValidator\Validator\RequestConformityValidator;
use Youshido\GraphQL\Validator\RequestValidator\Validator\VariablesValidator;
use Youshido\Tests\Schema\SchemaTest;
use Youshido\Tests\StarWars\Schema\StarWarsSchema;

class RequestValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Youshido\GraphQL\Exception\Parser\InvalidRequestException
     * @dataProvider invalidRequestProvider
     *
     * @param array $data
     */
    public function testInvalidRequests($data)
    {
        $parserResult = new ParseResult();
        if (isset($data['queries'])) {
            $parserResult->setQueries($data['queries']);
        }
        if (isset($data['fragmentReferences'])) {
            $parserResult->setFragmentReferences($data['fragmentReferences']);
        }
        if (isset($data['fragments'])) {
            $parserResult->setFragments($data['fragments']);
        }
        if (isset($data['variables'])) {
            $parserResult->setVariables($data['variables']);
        }
        if (isset($data['variableReferences'])) {
            $parserResult->setVariableReferences($data['variableReferences']);
        }

        $request = new Request($parserResult, isset($data['requestVariables']) ? $data['requestVariables'] : []);

        (new RequestValidator([
            new FragmentsValidator(),
            new VariablesValidator(),
        ]))->validate($request, new StarWarsSchema());
    }

    public function invalidRequestProvider()
    {
        $variable1 = (new Variable('test', 'Int', false, false, true, new Location(1, 1)))->setUsed(true);
        $variable2 = (new Variable('test2', 'Int', false, false, true, new Location(1, 1)))->setUsed(true);
        $variable3 = (new Variable('test3', 'Int', false, false, true, new Location(1, 1)))->setUsed(false);

        return [
            [
                [
                    'queries'            => [
                        new Query('test', null, [], [
                            new FragmentReference('reference', new Location(1, 1)),
                        ], [], new Location(1, 1)),
                    ],
                    'fragmentReferences' => [
                        new FragmentReference('reference', new Location(1, 1)),
                    ],
                ],
            ],
            [
                [
                    'queries'            => [
                        new Query('test', null, [], [
                            new FragmentReference('reference', new Location(1, 1)),
                            new FragmentReference('reference2', new Location(1, 1)),
                        ], [], new Location(1, 1)),
                    ],
                    'fragments'          => [
                        new Fragment('reference', 'TestType', [], [], new Location(1, 1)),
                    ],
                    'fragmentReferences' => [
                        new FragmentReference('reference', new Location(1, 1)),
                        new FragmentReference('reference2', new Location(1, 1)),
                    ],
                ],
            ],
            [
                [
                    'queries'            => [
                        new Query('test', null, [], [
                            new FragmentReference('reference', new Location(1, 1)),
                        ], [], new Location(1, 1)),
                    ],
                    'fragments'          => [
                        new Fragment('reference', 'TestType', [], [], new Location(1, 1)),
                        new Fragment('reference2', 'TestType', [], [], new Location(1, 1)),
                    ],
                    'fragmentReferences' => [
                        new FragmentReference('reference', new Location(1, 1)),
                    ],
                ],
            ],
            [
                [
                    'queries'            => [
                        new Query('test', null,
                            [
                                new Argument('test', new VariableReference('test', null, new Location(1, 1)), new Location(1, 1)),
                            ],
                            [
                                new Field('test', null, [], [], new Location(1, 1)),
                            ],
                            [],
                            new Location(1, 1)
                        ),
                    ],
                    'variableReferences' => [
                        new VariableReference('test', null, new Location(1, 1)),
                    ],
                    'requestVariables'   => ['test' => 1],
                ],
            ],
            [
                [
                    'queries'            => [
                        new Query('test', null, [
                            new Argument('test', new VariableReference('test', $variable1, new Location(1, 1)), new Location(1, 1)),
                            new Argument('test2', new VariableReference('test2', $variable2, new Location(1, 1)), new Location(1, 1)),
                        ], [
                            new Field('test', null, [], [], new Location(1, 1)),
                        ], [], new Location(1, 1)),
                    ],
                    'variables'          => [
                        $variable1,
                        $variable2,
                        $variable3,
                    ],
                    'variableReferences' => [
                        new VariableReference('test', $variable1, new Location(1, 1)),
                        new VariableReference('test2', $variable2, new Location(1, 1)),
                    ],
                    'requestVariables'   => ['test' => 1, 'test2' => 2],
                ],
            ],
        ];
    }

}
