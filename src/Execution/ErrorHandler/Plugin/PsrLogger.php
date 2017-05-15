<?php

namespace Youshido\GraphQL\Execution\ErrorHandler\Plugin;

use Psr\Log\LoggerInterface;
use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\ErrorHandler\ErrorHandlerMiddlewareInterface;

class PsrLogger implements ErrorHandlerMiddlewareInterface
{
    /** @var LoggerInterface  */
    private $logger;

    private $logLevel;

    public function __construct(LoggerInterface $logger, $logLevel)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    public function execute($error, ExecutionContext $executionContext, callable $next)
    {
        $this->logger->log($this->logLevel, $error);

        return $next($next, $executionContext);
    }
}
