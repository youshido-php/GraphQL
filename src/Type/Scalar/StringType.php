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
 * created: 11/27/15 1:05 AM
 */

namespace Youshido\GraphQL\Type\Scalar;

class StringType extends AbstractScalarType
{
    public function getName()
    {
        return 'String';
    }

    public function serialize($value)
    {
        if (true === $value) {
            return 'true';
        }

        if (false === $value) {
            return 'false';
        }

        if (null === $value) {
            return;
        }

        if (\is_array($value)) {
            return '';
        }

        return (string) $value;
    }

    public function isValidValue($value)
    {
        return null === $value || \is_scalar($value) || ((\is_object($value) && \method_exists($value, '__toString')));
    }

    public function getDescription()
    {
        return 'The `String` scalar type represents textual data, represented as UTF-8 ' .
               'character sequences. The String type is most often used by GraphQL to ' .
               'represent free-form human-readable text.';
    }
}
