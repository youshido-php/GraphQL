<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 01.12.15.
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;

use Youshido\GraphQL\Exception\Interfaces\DatableExceptionInterface;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;

trait ErrorContainerTrait
{
    /** @var \Exception[] */
    protected $errors = [];

    public function addError(\Exception $exception)
    {
        $this->errors[] = $exception;

        return $this;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function mergeErrors(ErrorContainerInterface $errorContainer)
    {
        if ($errorContainer->hasErrors()) {
            foreach ($errorContainer->getErrors() as $error) {
                $this->addError($error);
            }
        }

        return $this;
    }

    public function getErrorsArray($inGraphQLStyle = true)
    {
        $errors = [];

        foreach ($this->errors as $error) {
            if ($inGraphQLStyle) {
                if ($error instanceof DatableExceptionInterface) {
                    $errors[] = \array_merge(
                        ['message' => $error->getMessage()],
                        $error->getData() ?: [],
                        $error->getCode() ? ['code' => $error->getCode()] : []
                    );

                    continue;
                }

                if ($error instanceof LocationableExceptionInterface) {
                    $errors[] = \array_merge(
                        ['message' => $error->getMessage()],
                        $error->getLocation() ? ['locations' => [$error->getLocation()->toArray()]] : [],
                        $error->getCode() ? ['code' => $error->getCode()] : []
                    );

                    continue;
                }

                $errors[] = \array_merge(
                    ['message' => $error->getMessage()],
                    $error->getCode() ? ['code' => $error->getCode()] : []
                );

                continue;
            }

            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    public function clearErrors()
    {
        $this->errors = [];

        return $this;
    }
}
