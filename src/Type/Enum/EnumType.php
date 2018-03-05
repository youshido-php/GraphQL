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
 * Date: 07.12.15.
 */

namespace Youshido\GraphQL\Type\Enum;

use Youshido\GraphQL\Config\Object\EnumTypeConfig;

final class EnumType extends AbstractEnumType
{
    public function __construct(array $config)
    {
        $this->config = new EnumTypeConfig($config, $this, true);
    }

    public function getValues()
    {
        return $this->getConfig()->getValues();
    }
}
