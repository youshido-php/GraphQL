<?php

namespace Youshido\GraphQL\Relay\Field;

use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

/**
 * Class GlobalIdField
 */
class GlobalIdField extends AbstractField
{
    /** @var  string */
    protected $typeName;

    /**
     * @param string $typeName
     */
    public function __construct($typeName)
    {
        $this->typeName = $typeName;

        $config = [
            'type'    => $this->getType(),
            'name'    => $this->getName(),
            'resolve' => [$this, 'resolve'],
        ];

        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'id';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'The ID of an object';
    }

    /**
     * @return NonNullType
     */
    public function getType()
    {
        return new NonNullType(new IdType());
    }

    /**
     * @inheritdoc
     */
    public function resolve($value, array $args, ResolveInfoInterface $info)
    {
        return $value ? Node::toGlobalId($this->typeName, $value['id']) : null;
    }
}
