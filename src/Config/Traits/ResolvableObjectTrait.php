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

        if (\is_array($value) && \array_key_exists($this->getName(), $value)) {
            return $value[$this->getName()];
        }

        if (\is_object($value)) {
            return TypeService::getPropertyValue($value, $this->getName());
        }

        if (TypeMap::KIND_SCALAR === $this->getType()->getNamedType()->getKind()) {
            return;
        }

        throw new \Exception(\sprintf('Property "%s" not found in resolve result', $this->getName()));
    }

    public function getResolveFunction()
    {
        return $this->getConfig()->getResolveFunction();
    }
}
