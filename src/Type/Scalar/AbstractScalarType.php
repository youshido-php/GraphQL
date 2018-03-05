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
 * created: 11/27/15 1:00 AM
 */

namespace Youshido\GraphQL\Type\Scalar;

use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractScalarType extends AbstractType
{
    use ConfigAwareTrait;

    public function getName()
    {
        $className = \get_class($this);

        return \mb_substr($className, \mb_strrpos($className, '\\') + 1, -4);
    }

    final public function getKind()
    {
        return TypeMap::KIND_SCALAR;
    }

    public function parseValue($value)
    {
        return $this->serialize($value);
    }

    public function isInputType()
    {
        return true;
    }
}
