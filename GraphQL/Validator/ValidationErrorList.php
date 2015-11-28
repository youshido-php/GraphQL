<?php
/**
 * Date: 25.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQLBundle\Validator;


use Youshido\GraphQLBundle\Validator\Exception\ValidationException;

class ValidationErrorList
{

    /** @var ValidationException[] */
    private $errors = [];

    public function addError(\Exception $exception)
    {
        $this->errors[] = $exception;
    }

    public function hasErrors()
    {
        return (bool)count($this->errors);
    }

    public function toArray()
    {
        $exceptions = [];

        foreach ($this->errors as $exception) {
            $exceptions[] = [
                'message' => $exception->getMessage()
            ];
        }

        return $exceptions;
    }

}