<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:24 AM
*/

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Traits\FinalTypesConfigTrait;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

final class ObjectType extends AbstractObjectType
{

    use FinalTypesConfigTrait;

    public function build($config) {}

    public function getNamedType()
    {
        return $this;
    }
}