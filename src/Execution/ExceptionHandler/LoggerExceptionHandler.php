<?php

namespace Youshido\GraphQL\ExceptionHandler;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;

/**
 * Class LoggerExceptionHandler
 */
class LoggerExceptionHandler extends AbstractExceptionHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $logLevel;

    /**
     * LoggerExceptionHandler constructor.
     *
     * @param LoggerInterface $logger
     * @param mixed           $logLevel
     */
    public function __construct(LoggerInterface $logger, $logLevel = LogLevel::ERROR)
    {
        $this->logger   = $logger;
        $this->logLevel = $logLevel;
    }

    /**
     * @param \Exception                $error
     * @param ExecutionContextInterface $executionContext
     */
    public function handle(\Exception $error, ExecutionContextInterface $executionContext)
    {
        $this->logger->log($this->logLevel, $error->getMessage());
    }
}
