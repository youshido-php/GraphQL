<?php

namespace Youshido\GraphQL\Type\Traits;

/**
 * Trait FieldsArgumentsAwareObjectTrait
 */
trait FieldsArgumentsAwareObjectTrait
{
    use FieldsAwareObjectTrait;

    protected $hasArgumentCache;

    public function addArguments($arguments)
    {
        return $this->getConfig()->addArguments($arguments);
    }

    public function removeArgument($name)
    {
        return $this->getConfig()->removeArgument($name);
    }

    /**
     * @param array|string      $argument
     * @param null|array|string $info
     *
     * @return $this
     */
    public function addArgument($argument, $info = null)
    {
        return $this->getConfig()->addArgument($argument, $info);
    }

    public function getArguments()
    {
        return $this->getConfig()->getArguments();
    }

    public function getArgument($name)
    {
        return $this->getConfig()->getArgument($name);
    }

    public function hasArgument($name)
    {
        return $this->getConfig()->hasArgument($name);
    }

    public function hasArguments()
    {
        return $this->hasArgumentCache === null ? ($this->hasArgumentCache = $this->getConfig()->hasArguments()) : $this->hasArgumentCache;
    }
}
