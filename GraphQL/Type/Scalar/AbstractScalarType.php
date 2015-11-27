<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:00 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


use Youshido\GraphQL\Type\AbstractType;

abstract class AbstractScalarType extends AbstractType
{

    public function getName()
    {
        $reflect = new \ReflectionClass($this);
        return substr($reflect->getShortName(), 0, -4);
    }

}