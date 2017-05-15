<?php

namespace Youshido\GraphQL\Execution\ErrorHandler;

use Youshido\GraphQL\Exception\InvalidErrorHandlerPluginException;
use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\ErrorHandler\Plugin\ErrorHandlerPluginInterface;

class ErrorHandler
{
    /**
     * @var callable
     */
    private $pluginChain;
    /**
     * @param ErrorHandlerPluginInterface[] $plugins
     */
    public function __construct(array $plugins)
    {
        $this->pluginChain = $this->createPluginChain($plugins);
    }

    public function handle($error, ExecutionContext $executionContext)
    {
        $pluginChain = $this->pluginChain;
        return $pluginChain($error, $executionContext);
    }

    private function createPluginChain($pluginList)
    {
        $lastCallable = function () {}; // Last callable is a NO-OP
        while ($plugin = array_pop($pluginList)) {
            if (! $plugin instanceof ErrorHandlerPluginInterface) {
                throw InvalidErrorHandlerPluginException::forPlugin($plugin);
            }
            $lastCallable = function ($error, ExecutionContext $executionContext) use ($plugin, $lastCallable) {
                return $plugin->execute($error, $executionContext, $lastCallable);
            };
        }
        return $lastCallable;
    }
}
