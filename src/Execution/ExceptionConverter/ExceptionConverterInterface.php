<?php

namespace Youshido\GraphQL\Execution\ExceptionConverter;

/**
 * Interface ExceptionConverterInterface
 */
interface ExceptionConverterInterface
{
    /**
     * @param \Exception[] $exceptions
     *
     * @return array
     */
    public function convert(array $exceptions);
}
