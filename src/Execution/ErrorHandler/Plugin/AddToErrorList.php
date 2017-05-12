<?php

namespace Youshido\GraphQL\Execution\ErrorHandler\Plugin;

use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\ErrorHandler\ErrorHandlerMiddlewareInterface;

class AddToErrorList implements ErrorHandlerMiddlewareInterface
{
    public function execute($error, ExecutionContext $executionContext, callable $next)
    {
        $executionContext->addError($error);

        return $next($error, $executionContext);
    }
}
