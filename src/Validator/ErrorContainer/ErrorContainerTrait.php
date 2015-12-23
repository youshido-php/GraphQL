<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;


use Youshido\GraphQL\Validator\Exception\DatableResolveException;

trait ErrorContainerTrait
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

    public function getErrorsArray($asObject = true)
    {
        $errors = [];

        foreach ($this->errors as $error) {
            if ($asObject) {
                if (is_object($error)) {
                    if ($error instanceof DatableResolveException) {
                        $errors[] = array_merge(
                            ['message' => $error->getMessage()],
                            $error->getData() ?: [],
                            $error->getCode() ? ['code' => $error->getCode()] : []
                        );
                    } else {
                        $errors[] = array_merge(
                            ['message' => $error->getMessage()],
                            $error->getCode() ? ['code' => $error->getCode()] : []
                        );
                    }
                } else {
                    $errors[] = ['message' => $error];
                }
            } else {
                if (is_object($error)) {
                    $errors[] = $error->getMessage();
                } else {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    public function clearErrors()
    {
        $this->errors = [];

        return $this;
    }

}