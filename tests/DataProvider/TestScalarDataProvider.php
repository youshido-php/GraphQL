<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 12:05 PM
*/

namespace Youshido\Tests\DataProvider;


class TestScalarDataProvider
{


    public static function getTypesList()
    {
        return [
            "Int",
            "String"
        ];
    }

    public static function getIntTestData()
    {
        return [
            [1, 1],
            [-5, -5],
            ["1", null],
            ["Test", null],
            [false, null],
            [1.5, null],
        ];
    }

    public static function getStringTestData()
    {
        return [
            ["Test", "Test"],
            [1, "1"],
            [true, "true"],
            [false, "false"],
        ];
    }

}