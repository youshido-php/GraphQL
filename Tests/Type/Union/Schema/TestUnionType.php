<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Type\Union\Schema;


use Youshido\GraphQL\Type\Union\AbstractUnionType;

class TestUnionType extends AbstractUnionType
{

    public function getName()
    {
        return 'TestUnionType';
    }

    public function resolveType($object)
    {
        if (isset($object['secondName'])) {
            return new FirstType();
        }

        return new SecondType();
    }

    public function getTypes()
    {
        return [new FirstType(), new SecondType()];
    }
}