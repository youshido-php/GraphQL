<?php

namespace Youshido\GraphQL\ExceptionHandler;

/**
 * Class AbstractExceptionHandler
 */
abstract class AbstractExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @param \Exception $error
     *
     * @return bool
     */
    public function isFinal(\Exception $error)
    {
        return false;
    }
}
