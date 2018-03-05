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
 * created: 11:54 AM 5/5/16
 */

namespace Youshido\GraphQL\Type\Union;

final class UnionType extends AbstractUnionType
{
    protected $isFinal = true;

    public function resolveType($object)
    {
        $callable = $this->getConfigValue('resolveType');

        return \call_user_func_array($callable, [$object]);
    }

    public function getTypes()
    {
        return $this->getConfig()->get('types', []);
    }
}
