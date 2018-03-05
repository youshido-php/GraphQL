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
 * created: 8/14/16 12:26 PM
 */

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

class TestTimeType extends AbstractScalarType
{
    public function getName()
    {
        return 'TestTime';
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

        return $value instanceof \DateTime ? $value->format('H:i:s') : $value;
    }

    public function isValidValue($value)
    {
        if (\is_object($value)) {
            return true;
        }

        $d = \DateTime::createFromFormat('H:i:s', $value);

        return $d && $d->format('H:i:s') === $value;
    }

    public function getDescription()
    {
        return 'Representation time in "H:i:s" format';
    }
}
