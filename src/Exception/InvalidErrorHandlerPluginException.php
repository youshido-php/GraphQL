<?php

namespace Youshido\GraphQL\Exception;

class InvalidErrorHandlerPluginException extends \InvalidArgumentException
{
    public static function forPlugin($plugin)
    {
        return new static(sprintf(
            'Cannot add "%s" to the error handler chain, it does not implement the ErrorHandlerPluginInterface interface.',
            is_object($plugin) ? get_class($plugin) : gettype($plugin)
        ));
    }
}
