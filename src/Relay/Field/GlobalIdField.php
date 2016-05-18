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
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class GlobalIdField extends AbstractField
{

    /** @var  string */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name = '')
    {
        $this->name = $name;

        //todo: think about this, I made it for Processor, line 372
        $config = [
            'type'    => $this->getType(),
            'name'    => $this->getName(),
            'resolve' => [$this, 'resolve']
        ];

        parent::__construct($config);
    }

    public function getName()
    {
        return 'id';
    }

    public function getDescription()
    {
        return 'The ID of an object';
    }

    public function getType()
    {
        return new NonNullType(new IdType());
    }

    /**
     * @inheritdoc
     */
    public function resolve($value, $args = [], $type = null)
    {
        return Node::toGlobalId($this->name ?: $type->getName() ?: get_class($type), $value['id']);
    }
}
