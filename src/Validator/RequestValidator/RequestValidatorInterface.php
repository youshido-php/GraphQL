<?php

namespace Youshido\GraphQL\Validator\RequestValidator;

use Youshido\GraphQL\Execution\Request;

/**
 * Interface RequestValidatorInterface
 */
interface RequestValidatorInterface
{
    /**
     * @param Request $request
     */
    public function validate(Request $request);
}
