<?php

namespace Youshido\GraphQL\Config;

use Youshido\GraphQL\Field\Field;

/**
 * Interface TypeConfigInterface
 */
interface TypeConfigInterface
{
    /**
     * @param Field|string $field
     * @param array        $info
     */
    public function addField($field, $info = null);

    public function getField($name);

    public function removeField($name);

    public function hasField($name);

    public function getFields();
}
