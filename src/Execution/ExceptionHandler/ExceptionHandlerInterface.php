<?php

namespace Youshido\GraphQL\ExceptionHandler;

use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;

/**
 * Interface ExceptionHandlerInterface
 */
interface ExceptionHandlerInterface
{
    /**
     * @param \Exception                $exception
     * @param ExecutionContextInterface $executionContext
     */
    public function handle(\Exception $exception, ExecutionContextInterface $executionContext);

    /**
     * @param \Exception $error
     *
     * @return bool
     */
    public function isFinal(\Exception $error);
}
