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
 * Date: 12.05.16.
 */

namespace Youshido\GraphQL\Type\InterfaceType;

use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;

final class InterfaceType extends AbstractInterfaceType
{
    public function __construct($config = [])
    {
        $this->config = new InterfaceTypeConfig($config, $this, true);
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function build($config): void
    {
    }

    public function resolveType($object)
    {
        return $this->getConfig()->resolveType($object);
    }
}
