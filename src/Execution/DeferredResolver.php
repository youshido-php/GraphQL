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

    /** @var callable */
    private $callback;

    /** @var $mixed */
    public $result;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Add a callback that will be applied when the result is fetched.
     *
     * @param $callback
     */
    public function setCallback($callback) {
        $this->callback = $callback;
    }

    public function resolve() {
        $result = call_user_func($this->resolver);
        $this->result = call_user_func($this->callback, $result);
    }
}