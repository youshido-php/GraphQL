<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 10:37 PM
*/

namespace Youshido\Tests\Library\Validator;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\GraphQL\Validator\ConfigValidator\Rules\TypeValidationRule;
use Youshido\Tests\DataProvider\TestObjectType;

class TypeValidationRuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param      $ruleInfo
     * @param      $data
     * @param bool $isValid
     *
     * @dataProvider simpleRules
     */
    public function testSimpleRules($ruleInfo, $data, $isValid = true)
    {
        $rule = new TypeValidationRule(new ConfigValidator());
        $this->assertEquals($isValid, $rule->validate($data, $ruleInfo));
    }

    public function simpleRules()
    {
        return [
            [TypeService::TYPE_ANY, null],

            [TypeService::TYPE_ANY_OBJECT, new StringType()],
            [TypeService::TYPE_ANY_OBJECT, null, false],

            [TypeService::TYPE_CALLABLE, function () {}],
            [TypeService::TYPE_CALLABLE, null, false],

            [TypeService::TYPE_BOOLEAN, true],
            [TypeService::TYPE_BOOLEAN, false],
            [TypeService::TYPE_BOOLEAN, null, false],

            [TypeService::TYPE_ARRAY, []],
            [TypeService::TYPE_ARRAY, null, false],

            [TypeService::TYPE_OBJECT_TYPE, new TestObjectType()],
            [TypeService::TYPE_OBJECT_TYPE, new StringType(), true],

            [TypeService::TYPE_FIELDS_LIST_CONFIG, ["name" => new StringType()]],
            [TypeService::TYPE_FIELDS_LIST_CONFIG, ["name" => TypeMap::TYPE_STRING]],
            [TypeService::TYPE_FIELDS_LIST_CONFIG, [new Field(['name' => 'id', 'type' => new StringType()])]],
            [TypeService::TYPE_FIELDS_LIST_CONFIG, [], false],

            [null, null, false],


        ];
    }

}
