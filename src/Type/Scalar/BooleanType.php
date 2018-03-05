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

class BooleanType extends AbstractScalarType
{
    public function getName()
    {
        return 'Boolean';
    }

    public function serialize($value)
    {
        if ('true' === $value) {
            return true;
        }

        if ('false' === $value) {
            return false;
        }

        return (bool) $value;
    }

    public function isValidValue($value)
    {
        return null === $value || \is_bool($value) || (\is_int($value) && (0 === $value || 1 === $value));
    }

    public function getDescription()
    {
        return 'The `Boolean` scalar type represents `true` or `false`.';
    }
}
