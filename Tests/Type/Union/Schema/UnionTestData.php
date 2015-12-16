<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Type\Union\Schema;


class UnionTestData
{

    public static function getList()
    {
        return [
            [
                'name'        => 'object 1',
                'description' => 'object description 1'
            ],
            [
                'name'        => 'object 2',
                'description' => 'object description 2',
                'secondName'  => 'object second name 2'
            ],
            [
                'name'        => 'object 3',
                'description' => 'object description 3'
            ],
            [
                'name'        => 'object 4',
                'description' => 'object description 4',
                'secondName'  => 'object second name 4'
            ],
        ];
    }

    public static function getOne()
    {
        return [
            'name'        => 'object one',
            'description' => 'object description one',
            'secondName'  => 'second name'
        ];
    }

}