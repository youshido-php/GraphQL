<?php

namespace Youshido\GraphQL\ExceptionHandler;

use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;

/**
 * Class FilteringExceptionHandler
 */
class FilteringExceptionHandler extends AbstractExceptionHandler
{
    /** @var callable */
    private $filterFn;

    /** @var ExceptionHandlerInterface */
    private $errorHandler;

    /**
     * FilteringExceptionHandler constructor.
     *
     * @param callable                  $filter
     * @param ExceptionHandlerInterface $errorHandler
     */
    public function __construct(callable $filter, ExceptionHandlerInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;
        $this->filterFn     = $filter;
    }

    /**
     * @param \Exception                $error
     * @param ExecutionContextInterface $executionContext
     */
    public function handle(\Exception $error, ExecutionContextInterface $executionContext)
    {
        $fn = $this->filterFn;

        if ($fn($error, $executionContext)) {
            $this->errorHandler->handle($error, $executionContext);
        }
    }
}
