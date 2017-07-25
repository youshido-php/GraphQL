<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/24/17 11:50 PM
 */

namespace Youshido\GraphQL\Execution;


/**
 * Default implementation of DeferredResolverInterface
 *
 * @package Youshido\GraphQL\Execution
 */
class DeferredResolver implements DeferredResolverInterface {

    /** @var callable */
    private $resolver;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolve() {
      return call_user_func($this->resolver);
    }
}