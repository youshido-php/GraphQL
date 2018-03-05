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
 * created: 11/27/15 1:24 AM
 */

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;

final class ObjectType extends AbstractObjectType
{
    public function __construct(array $config)
    {
        $this->config = new ObjectTypeConfig($config, $this, true);
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function build($config): void
    {
    }

    public function getName()
    {
        return $this->getConfigValue('name');
    }
}
