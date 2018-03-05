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
 * created: 12/1/15 1:22 AM
 */

namespace Youshido\GraphQL\Type\ListType;

use Youshido\GraphQL\Config\Object\ListTypeConfig;

final class ListType extends AbstractListType
{
    public function __construct($itemType)
    {
        $this->config = new ListTypeConfig(['itemType' => $itemType], $this, true);
    }

    public function getItemType()
    {
        return $this->getConfig()->get('itemType');
    }

    public function getName()
    {
    }
}
