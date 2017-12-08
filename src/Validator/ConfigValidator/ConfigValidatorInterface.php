<?php

namespace Youshido\GraphQL\Validator\ConfigValidator;

/**
 * Interface ConfigValidatorInterface
 */
interface ConfigValidatorInterface
{
    public function validate($data, $rules = [], $allowExtraFields = null);
}
