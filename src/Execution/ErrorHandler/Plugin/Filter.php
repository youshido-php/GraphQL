<?php

namespace Youshido\GraphQL\Execution\ErrorHandler\Plugin;

use Youshido\GraphQL\Execution\Context\ExecutionContext;

class Filter implements ErrorHandlerPluginInterface
{
    /** @var callable  */
    private $filter;

    public function __construct(callable $filter)
    {
        $this->filter = $filter;
    }

    public function execute($error, ExecutionContext $executionContext, callable $next)
    {
        $filter = $this->filter;
        if (false === $filter($error, $executionContext)) {
            return null;
        }

        return $next($next, $executionContext);
    }
}
