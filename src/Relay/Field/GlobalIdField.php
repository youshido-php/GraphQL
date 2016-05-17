<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:23 PM
*/

namespace Youshido\GraphQL\Relay\Field;


use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class GlobalIdField extends AbstractField
{
    private $typeName = null;

    /**
     * GlobalIdField constructor.
     * @param string $typeName
     */
    public function __construct($typeName = null)
    {

        $config         = [
            'name'        => 'id',
            'description' => 'The ID of an object',
        ];
        $this->typeName = $typeName;
        parent::__construct($config);
    }

    public function getType()
    {
        return new NonNullType(new IdType());
    }

    /**
     * @param              $value
     * @param              $args
     * @param AbstractType $type
     * @return string
     */
    public function resolve($value, $args, $type)
    {
        return Node::toGlobalId($type->getName() ?: get_class($type), $value);
    }


}
