<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * This file is a part of PhpStorm project.
 */

namespace Youshido\GraphQL\Type;

class SchemaTypesList
{
    private $typesList = [];

    /**
     * @param array $types
     *
     * @throws
     *
     * @return $this
     */
    public function addTypes($types)
    {
        if (!\is_array($types)) {
            throw new \Exception('addTypes accept only array of types');
        }

        foreach ($types as $type) {
            $this->addType($type);
        }

        return $this;
    }

    public function getTypes()
    {
        return $this->typesList;
    }

    /**
     * @param TypeInterface $type
     *
     * @return $this
     */
    public function addType(TypeInterface $type)
    {
        $typeName = $this->getTypeName($type);

        if ($this->isTypeNameRegistered($typeName)) {
            return $this;
        }

        $this->typesList[$typeName] = $type;

        return $this;
    }

    public function isTypeNameRegistered($typeName)
    {
        return isset($this->typesList[$typeName]);
    }

    private function getTypeName($type)
    {
        if (\is_string($type)) {
            return $type;
        }

        if (\is_object($type) && $type instanceof AbstractType) {
            return $type->getName();
        }

        throw new \Exception('Invalid type passed to Schema');
    }
}
