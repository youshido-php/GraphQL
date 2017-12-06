<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Execution\ResolveInfo;

/**
 * Interface FieldInterface
 */
interface FieldInterface extends InputFieldInterface
{
    /**
     * @param mixed       $value
     * @param array       $args
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function resolve($value, array $args, ResolveInfo $info);

    /**
     * @return callable|null
     */
    public function getResolveFunction();
}
