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
     *
     * @return mixed
     */
    public function setValue($value);
}
