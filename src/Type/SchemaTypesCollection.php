<?php

namespace Youshido\GraphQL\Type;

/**
 * Class SchemaTypesCollection
 */
class SchemaTypesCollection
{
    /** @var TypeInterface[] */
    private $types = [];

    /**
     * @param array $types
     */
    public function addMany(array $types)
    {
        foreach ($types as $type) {
            $this->add($type);
        }
    }

    /**
     * @return TypeInterface[]
     */
    public function all()
    {
        return $this->types;
    }

    /**
     * @param TypeInterface $type
     */
    public function add(TypeInterface $type)
    {
        $this->types[$type->getName()] = $type;
    }
}
