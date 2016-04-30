<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 3:40 PM 4/29/16
 */

namespace Youshido\GraphQL\Type;


class NonNullType extends AbstractType implements CompositeTypeInterface
{
    private $_typeOf;

    /**
     * NonNullType constructor.
     * @param AbstractType $fieldType
     */
    public function __construct($fieldType)
    {
        $this->_typeOf = $fieldType;
    }

    public function checkBuild()
    {
    }

    public function getName()
    {
        return null;
    }

    public function getKind()
    {
        return TypeMap::KIND_NON_NULL;
    }

    public function resolve($value)
    {
        return $value;
    }

    public function isValidValue($value)
    {
        return $value !== null;
    }

    public function getTypeOf()
    {
        return $this->_typeOf;
    }


}