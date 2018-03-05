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

/**
 * @deprecated USE DateTime type instead. To be removed in 1.4.
 *
 * Class DateType
 */
class DateType extends AbstractScalarType
{
    public function getName()
    {
        return 'Date';
    }

    /**
     * @param $value \DateTime
     *
     * @return string|null
     */
    public function serialize($value)
    {
        if (null === $value) {
            return;
        }

        return $value->format('Y-m-d');
    }

    public function isValidValue($value)
    {
        if (null === $value || \is_object($value)) {
            return true;
        }

        $d = \DateTime::createFromFormat('Y-m-d', $value);

        return $d && $d->format('Y-m-d') === $value;
    }

    public function getDescription()
    {
        return 'DEPRECATED. Use DateTime instead';
    }
}
