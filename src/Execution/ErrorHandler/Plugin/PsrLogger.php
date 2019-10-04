<?php

namespace Youshido\GraphQL\Execution\ErrorHandler\Plugin;

use Psr\Log\LoggerInterface;
use Youshido\GraphQL\Execution\Context\ExecutionContext;

class PsrLogger implements ErrorHandlerPluginInterface
{
    /** @var LoggerInterface  */
    private $logger;

    /** @var  mixed */
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
