<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:00 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Object\ScalarTypeConfig;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractScalarType extends AbstractType
{

    public function getName()
    {
        $className = get_class($this);

        return strtolower(substr($className, strrpos($className, '\\') + 1, -4));
    }

    final public function getKind()
    {
        return TypeMap::KIND_SCALAR;
    }

}