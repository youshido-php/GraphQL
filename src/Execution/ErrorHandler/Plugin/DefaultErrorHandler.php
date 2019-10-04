<?php

namespace Youshido\GraphQL\Execution\ErrorHandler\Plugin;

use Youshido\GraphQL\Execution\Context\ExecutionContext;

class DefaultErrorHandler implements ErrorHandlerPluginInterface
{
    public function execute($error, ExecutionContext $executionContext, callable $next)
    {
        $executionContext->addError($error);

        return $next($error, $executionContext);
    }
}
