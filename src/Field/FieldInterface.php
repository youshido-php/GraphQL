<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\TypeInterface;

/**
 * Interface FieldInterface
 */
interface FieldInterface extends ArgumentsContainerInterface
{
    /**
     * @return TypeInterface
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param mixed                $value
     * @param array                $args
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
