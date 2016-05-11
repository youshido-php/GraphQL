<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:23 PM
*/

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

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

    public function resolve($value, $args, $type)
    {
        if (empty($args)) $args = [];
        $args['typeName'] = $this->typeName;
        //       return toGlobalId(typeName || info.parentType.name, idFetcher ? idFetcher(obj, context, info) : obj.id);
        return parent::resolve($value, $args, $type);
    }


}
