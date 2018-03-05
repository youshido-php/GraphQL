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
 * created: 9/29/16 10:41 PM
 */

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Config\AbstractConfig;

interface InputTypeInterface
{
    /**
     * @return string type name
     */
    public function getName();

    /**
     * @return string predefined type kind
     */
    public function getKind();

    /**
     * @return string type description
     */
    public function getDescription();

    /**
     * Coercing value received as input to current type.
     *
     * @param $value
     *
     * @return mixed
     */
    public function parseValue($value);

    /**
     * Coercing result to current type.
     *
     * @param $value
     *
     * @return mixed
     */
    public function serialize($value);

    /**
     * @param $value mixed
     *
     * @return bool
     */
    public function isValidValue($value);

    /**
     * @return AbstractConfig
     */
    public function getConfig();
}
