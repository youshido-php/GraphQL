<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;


class ErrorContainer implements ErrorContainerInterface
{

    /** @var \Exception[] */
    protected $errors = [];

    public function addError(\Exception $exception)
    {
        $this->errors[] = $exception;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsArray()
    {
        $errors = [];

        foreach ($this->errors as $error) {
            $errors[] = is_object($error) ? $error->getMessage() : $error;
        }

        return $errors;
    }

    public function clearErrors()
    {
        $this->errors = [];

        return $this;
    }

}