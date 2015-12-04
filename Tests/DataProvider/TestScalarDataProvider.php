<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 12:05 PM
*/

namespace Youshido\Tests\DataProvider;
require_once __DIR__ . '/../../vendor/autoload.php';

class TestScalarDataProvider
{


    public static function getTypesList()
    {
        return [
            "IntType",
            "StringType",
            "BooleanType",
            "FloatType"
        ];
    }

    public static function getIntTypeTestData()
    {
        /** input, serialization, isValid */
        return [
            [1, 1, true],
            [0, 0, true],
            [-1, -1, true],
            [0.1, 0, false],
            [1.1, 1, false],
            [1e5, 100000, false],
            ["1", 1, false],
            [9876504321, 9876504321, true],
            [-9876504321, -9876504321, true],
            [1e100, null, false],
            [-1e100, null, false],
            ['-1.1', -1, false],
            ['one', null, false],
            [false, 0, false],
            [true, 1, false],
        ];
    }

    public static function getFloatTypeTestData()
    {
        return [
            [1, 1.0, true],
            [0, 0.0, true],
            [-1, -1, true],
            [0.1, 0.1, true],
            [1.1, 1.1, true],
            ['-1.1', -1.1, false],
            ['one', null, false],
            [false, 0.0, false],
            [true, 1.0, false],
        ];
    }

    public static function getStringTypeTestData()
    {
        return [
            ["string", "string", true],
            [1, "1", false],
            [1.1, "1.1", false],
            [true, "true", false],
            [false, "false", false],
        ];
    }

    public static function getBooleanTypeTestData()
    {
        return [
            ["string", true, false],
            ["", false, false],
            [1, true, false],
            [0, false, false],
            [null, false, false],
            [true, true, true],
            [false, false, true],
        ];
    }

}