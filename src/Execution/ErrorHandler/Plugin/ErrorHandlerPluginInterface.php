<?php

namespace Youshido\GraphQL\Execution\ErrorHandler\Plugin;

use Youshido\GraphQL\Execution\Context\ExecutionContext;

interface ErrorHandlerPluginInterface
{
    public function execute($error, ExecutionContext $context, callable $next);
}
