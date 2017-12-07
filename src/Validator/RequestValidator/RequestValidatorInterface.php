<?php

namespace Youshido\GraphQL\Validator\RequestValidator;

use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;

/**
 * Interface RequestValidatorInterface
 */
interface RequestValidatorInterface
{
    /**
     * @param Request        $request
     * @param AbstractSchema $schema
     */
    public function validate(Request $request, AbstractSchema $schema);
}
