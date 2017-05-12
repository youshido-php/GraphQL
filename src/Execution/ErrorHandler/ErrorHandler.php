<?php

namespace Youshido\GraphQL\Execution\ErrorHandler;

use Youshido\GraphQL\Exception\InvalidErrorHandlerMiddlewareException;
use Youshido\GraphQL\Execution\Context\ExecutionContext;

class ErrorHandler
{
    /**
     * @var callable
     */
    private $middlewareChain;
    /**
     * @param ErrorHandlerMiddlewareInterface[] $middleware
     */
    public function __construct(array $middleware)
    {
        $this->middlewareChain = $this->createExecutionChain($middleware);
    }

    public function handle($error, ExecutionContext $executionContext)
    {
        $middlewareChain = $this->middlewareChain;
        return $middlewareChain($error, $executionContext);
    }

    private function createExecutionChain($middlewareList)
    {
        $lastCallable = function () {}; // Last callable is a NO-OP
        while ($middleware = array_pop($middlewareList)) {
            if (! $middleware instanceof ErrorHandlerMiddlewareInterface) {
                throw InvalidErrorHandlerMiddlewareException::forMiddleware($middleware);
            }
            $lastCallable = function ($error, ExecutionContext $executionContext) use ($middleware, $lastCallable) {
                return $middleware->execute($error, $executionContext, $lastCallable);
            };
        }
        return $lastCallable;
    }
}
