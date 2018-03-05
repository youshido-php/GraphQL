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
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 9/29/16 10:32 PM
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\AbstractType;

interface InputFieldInterface
{
    /**
     * @return AbstractType
     */
    public function getType();

    public function getName();

    public function addArguments($argumentsList);

    public function removeArgument($argumentName);

    public function addArgument($argument, $ArgumentInfo = null);

    /**
     * @return AbstractType[]
     */
    public function getArguments();

    /**
     * @param string $argumentName
     *
     * @return AbstractType
     */
    public function getArgument($argumentName);

    /**
     * @param string $argumentName
     *
     * @return bool
     */
    public function hasArgument($argumentName);

    /**
     * @return bool
     */
    public function hasArguments();
}
