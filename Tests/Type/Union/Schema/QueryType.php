<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Type\Union\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class QueryType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
        // TODO: Implement resolve() method.
    }

    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('unionList', new ListType(new TestUnionType()), [
                'resolve' => function() {
                    return UnionTestData::getList();
                }
            ])
            ->addField('oneUnion', new TestUnionType(), [
                'resolve' => function () {
                    return UnionTestData::getOne();
                }
            ]);
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return 'RootQueryType';
    }
}