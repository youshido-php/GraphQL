<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\DataProvider;


use Youshido\GraphQL\Field\AbstractInputField;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class TestInputField extends AbstractInputField
{

    /**
     * @return AbstractInputObjectType
     */
    public function getType()
    {
        return new IntType();
    }

    public function getDescription()
    {
        return 'description';
    }

    public function getDefaultValue()
    {
        return 'default';
    }
}