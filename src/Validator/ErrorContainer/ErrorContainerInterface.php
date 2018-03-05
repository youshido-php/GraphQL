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
 * Date: 27.11.15.
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;

interface ErrorContainerInterface
{
    public function addError(\Exception $exception);

    public function mergeErrors(self $errorContainer);

    public function hasErrors();

    public function getErrors();

    public function getErrorsArray();

    public function clearErrors();
}
