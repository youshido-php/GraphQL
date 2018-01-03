<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\DataProvider;


use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

class TestUnionType extends AbstractUnionType
{

    public function getTypes()
    {
        return [
            new TestObjectType(),
        ];
    }

    public function resolveType($object, ResolveInfoInterface $resolveInfo)
    {
        return $object;
    }

    public function getDescription()
    {
        return 'Union collect cars types';
    }


}
