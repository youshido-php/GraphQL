<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\DataProvider;


use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

class TestUnionType extends AbstractUnionType
{

    public function getTypes()
    {
        return [
            new IntType(),
            new StringType()
        ];
    }

    public function resolveType($object)
    {
        return $object;
    }

    public function getDescription()
    {
        return 'Union collect cars types';
    }


}