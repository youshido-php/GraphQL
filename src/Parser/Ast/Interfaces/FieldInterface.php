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
 * Date: 03.11.16.
 */

namespace Youshido\GraphQL\Parser\Ast\Interfaces;

use Youshido\GraphQL\Parser\Ast\Argument;

interface FieldInterface extends LocatableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @return Argument[]
     */
    public function getArguments();

    /**
     * @param string $name
     *
     * @return Argument
     */
    public function getArgument($name);

    /**
     * @return bool
     */
    public function hasFields();

    /**
     * @return array
     */
    public function getFields();
}
