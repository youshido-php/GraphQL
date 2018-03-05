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
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11/27/15 1:22 AM
 */

namespace Youshido\GraphQL\Type\Scalar;

class IntType extends AbstractScalarType
{
    public function getName()
    {
        return 'Int';
    }

    public function serialize($value)
    {
        if (null === $value) {
            return;
        }

        if (\is_int($value)) {
            return $value;
        }
        $value = (int) $value;

        return 0 !== $value ? $value : null;
    }

    public function isValidValue($value)
    {
        return null === $value || \is_int($value);
    }

    public function getDescription()
    {
        return 'The `Int` scalar type represents non-fractional signed whole numeric ' .
               'values. Int can represent values between -(2^53 - 1) and 2^53 - 1 since ' .
               'represented in JSON as double-precision floating point numbers specified' .
               'by [IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point).';
    }
}
