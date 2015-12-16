<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Type\Union\Schema;


use Youshido\GraphQL\Type\Object\AbstractUnionType;

class TestUnionType extends AbstractUnionType
{

    public function getName()
    {
        return 'TestUnionType';
    }

    public function resolveType($object)
    {
        return new FirstType();
    }

    public function getTypes()
    {
        return [new FirstType(), new SecondType()];
    }
}