<?php

namespace Youshido\GraphQL\Execution\ErrorHandler;

use Youshido\GraphQL\Execution\Context\ExecutionContext;

interface ErrorHandlerMiddlewareInterface
{
    public function execute($error, ExecutionContext $context, callable $next);
}
