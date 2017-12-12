<?php

namespace Youshido\GraphQL\Validator\ErrorContainer;

/**
 * Trait ErrorContainerTrait
 */
trait ErrorContainerTrait
{
    /** @var \Exception[] */
    protected $errors = [];

    /**
     * @param \Exception $exception
     */
    public function addError(\Exception $exception)
    {
        $this->errors[] = $exception;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return (bool) $this->errors;
    }

    /**
     * @return \Exception[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Clear all errors
     */
    public function clearErrors()
    {
        $this->errors = [];
    }
}
