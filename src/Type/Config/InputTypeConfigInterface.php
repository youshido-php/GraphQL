<?php
/**
 * Date: 17.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Config;


interface InputTypeConfigInterface
{

    /**
     * @param       $name
     * @param       $type
     * @param array $config
     *
     * @return TypeConfigInterface
     */
    public function addField($name, $type, $config = []);

    public function getField($name);

    public function removeField($name);

    public function hasField($name);

    public function getFields();

}