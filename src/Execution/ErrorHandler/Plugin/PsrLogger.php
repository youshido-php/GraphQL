<?php

namespace Youshido\GraphQL\Execution\ErrorHandler\Plugin;

use Psr\Log\LoggerInterface;
use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\ErrorHandler\ErrorHandlerMiddlewareInterface;

class PsrLogger implements ErrorHandlerMiddlewareInterface
{
    /** @var LoggerInterface  */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute($error, ExecutionContext $executionContext, callable $next)
    {
        $this->logger->error($error);

        return $next($next, $executionContext);
    }
}
