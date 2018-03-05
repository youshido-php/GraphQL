<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Philipp Melab <philipp.melab@gmail.com>
*/

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

trait ResolvableObjectTrait
{

    public function resolve($value, array $args, ResolveInfo $info)
    {
        if ($resolveFunction = $this->getConfig()->getResolveFunction()) {
            return $resolveFunction($value, $args, $info);
        }

        if (is_array($value) && array_key_exists($this->getName(), $value)) {
            return $value[$this->getName()];
        }
        if (is_object($value)) {
            return TypeService::getPropertyValue($value, $this->getName());
        }
        if ($this->getType()->getNamedType()->getKind() == TypeMap::KIND_SCALAR) {
            return null;
        }

        throw new \Exception(sprintf('Property "%s" not found in resolve result', $this->getName()));
    }


    public function getResolveFunction()
    {
        return $this->getConfig()->getResolveFunction();
    }
}
