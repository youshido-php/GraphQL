<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Execution\ResolveInfo;

/**
 * Interface FieldInterface
 */
interface FieldInterface extends InputFieldInterface
{
    public function resolve($value, array $args, ResolveInfo $info);

    public function getResolveFunction();
}
