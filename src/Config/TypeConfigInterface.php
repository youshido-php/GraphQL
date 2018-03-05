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
 * Date: 17.12.15.
 */

namespace Youshido\GraphQL\Config;

use Youshido\GraphQL\Field\Field;

interface TypeConfigInterface
{
    /**
     * @param Field|string $field
     * @param array        $fieldInfo
     */
    public function addField($field, $fieldInfo = null);

    public function getField($name);

    public function removeField($name);

    public function hasField($name);

    public function getFields();
}
