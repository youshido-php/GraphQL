<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/24/17 11:50 PM
 */

namespace Youshido\GraphQL\Execution;


class DeferredResolver
{

    /** @var callable */
    private $resolver;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolve() {
        return ($this->resolver)();
    }
}