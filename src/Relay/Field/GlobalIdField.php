<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:23 PM
*/

namespace Youshido\GraphQL\Relay\Field;


use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

/**
 * todo: I think its not good to create Field instance and replace it with type.
 * todo: I think there must be only one interface: addFields($name, $type (not field))
 * todo: and using field like a type not work at this moment
 */

class GlobalIdField extends Field
{
    private $typeName = null;

    /**
     * GlobalIdField constructor.
     * @param string $typeName
     */
    public function __construct($typeName = null) {

        $config         = [
            'name' => 'id',
            'description' => 'The ID of an object',
            'type' => new NonNullType(new IdType()),
        ];
        $this->typeName = $typeName;
        parent::__construct($config);
    }

    /**
     * @param $value
     * @param $args
     * @param AbstractType $type
     * @return string
     */
    public function resolve($value, $args, $type)
    {
        return Node::toGlobalId($type->getName() ?: get_class($type), $value);
    }


}
