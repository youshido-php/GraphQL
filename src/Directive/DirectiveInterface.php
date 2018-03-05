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
 * Date: 3/17/17.
 */

namespace Youshido\GraphQL\Directive;

use Youshido\GraphQL\Type\AbstractType;

interface DirectiveInterface
{
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
