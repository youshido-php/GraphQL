<?php

namespace Youshido\GraphQL\Execution\ExceptionConverter;

use Youshido\GraphQL\Exception\Interfaces\DatableExceptionInterface;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;

/**
 * Class ExceptionConverter
 */
class ExceptionConverter implements ExceptionConverterInterface
{
    /** @var  bool */
    private $debug;

    /**
     * ExceptionConverter constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * @param \Exception[] $exceptions
     *
     * @return array
     */
    public function convert(array $exceptions)
    {
        $errors = [];
        foreach ($exceptions as $exception) {
            $error = [
                'message' => $exception->getMessage(),
            ];

            if ($exception->getCode()) {
                $error['code'] = $exception->getCode();
            }

            if ($exception instanceof LocationableExceptionInterface && $exception->getLocation()) {
                $error['locations'] = [$exception->getLocation()->toArray()];
            }

            if ($exception instanceof DatableExceptionInterface) {
                $error = array_merge($error, $exception->getData());
            }

            if ($this->debug) {
                $error['line']  = $exception->getLine();
                $error['file']  = $exception->getFile();
                $error['trace'] = $exception->getTraceAsString();
            }

            $errors[] = $error;
        }

        return $errors;
    }
}
