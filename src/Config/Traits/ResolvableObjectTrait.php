<?php

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Exception\ResolveException;
use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

/**
 * Trait ResolvableObjectTrait
 */
trait ResolvableObjectTrait
{
    /**
     * @param mixed       $value
     * @param array       $args
     * @param ResolveInfoInterface $info
     *
     * @return mixed|null
     * @throws ResolveException
     */
    public function resolve($value, array $args, ResolveInfoInterface $info)
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

        if ($this->getType()->getNamedType()->getKind() === TypeMap::KIND_SCALAR) {
            return null;
        }

        throw new ResolveException(sprintf('Property "%s" not found in resolve result', $this->getName()));
    }

    /**
     * @return callable|null
     */
    public function getResolveFunction()
    {
        return $this->getConfig()->getResolveFunction();
    }
}
