<?php

namespace Youshido\GraphQL\Validator\RequestValidator;

use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;

/**
 * Class RequestValidator
 */
class RequestValidator implements RequestValidatorInterface
{
    /** @var  RequestValidatorInterface[] */
    private $validators = [];

    /**
     * RequestValidator constructor.
     *
     * @param RequestValidatorInterface[] $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * @param Request        $request
     * @param AbstractSchema $schema
     */
    public function validate(Request $request, AbstractSchema $schema)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($request, $schema);
        }
    }

    /**
     * @return RequestValidatorInterface[]
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param RequestValidatorInterface[] $validators
     */
    public function setValidators($validators)
    {
        $this->validators = $validators;
    }
}
