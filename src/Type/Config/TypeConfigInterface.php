<?php
/**
 * Date: 02.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Type\Config\Field\FieldConfig;

interface TypeConfigInterface extends InputTypeConfigInterface
{

    /**
     * @param       $name
     * @param       $type
     * @param array $config
     *
     * @return TypeConfigInterface
     */
    public function addField($name, $type, $config = []);

    /**
     * @param $name
     *
     * @return FieldConfig
     */
    public function getField($name);

    public function removeField($name);

    public function hasField($name);

    public function getFields();
}