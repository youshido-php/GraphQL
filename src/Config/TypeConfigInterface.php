<?php

namespace Youshido\GraphQL\Config;

use Youshido\GraphQL\Field\FieldInterface;

/**
 * Interface TypeConfigInterface
 */
interface TypeConfigInterface
{
    /**
     * @param FieldInterface|string $field
     * @param array                 $info
     */
    public function addField($field, $info = null);

    /**
     * @param string $name
     *
     * @return FieldInterface
     */
    public function getField($name);

    /**
     * @param string $name
     */
    public function removeField($name);

    /**
     * @param string $name
     */
    public function hasField($name);

    /**
     * @return FieldInterface[]
     */
    public function getFields();
}
