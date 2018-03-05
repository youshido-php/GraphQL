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
 * Class TimestampType.
 *
 * @deprecated Should not be used, to be removed in 1.5
 */
class TimestampType extends AbstractScalarType
{
    public function getName()
    {
        return 'Timestamp';
    }

    /**
     * @param $value \DateTime
     *
     * @return string|null
     */
    public function serialize($value)
    {
        if (null === $value || !\is_object($value)) {
            return;
        }

        return $value->getTimestamp();
    }

    public function isValidValue($value)
    {
        if (null === $value || \is_object($value)) {
            return true;
        }

        return \is_int($value);
    }

    public function getDescription()
    {
        return 'DEPRECATED. Will be converted to a real timestamp';
    }
}
