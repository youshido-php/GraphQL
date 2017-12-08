<?php

namespace Youshido\GraphQL\Validator\ErrorContainer;

/**
 * Interface ErrorContainerInterface
 */
interface ErrorContainerInterface
{
    public function addError(\Exception $exception);

    public function mergeErrors(ErrorContainerInterface $errorContainer);

    public function hasErrors();

    public function getErrors();

    public function getErrorsArray();

    public function clearErrors();
}
