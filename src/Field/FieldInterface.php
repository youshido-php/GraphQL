<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;

/**
 * Interface FieldInterface
 */
interface FieldInterface extends InputFieldInterface
{
    /**
     * @param mixed       $value
     * @param array       $args
     * @param ResolveInfoInterface $info
     *
     * @return mixed
     */
    public function resolve($value, array $args, ResolveInfoInterface $info);

    /**
     * @return callable|null
     */
    public function getResolveFunction();
}
