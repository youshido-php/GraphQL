<?php

namespace Youshido\GraphQL\Parser\Ast\Interfaces;

/**
 * Interface ValueInterface
 */
interface ValueInterface
{
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);
}
