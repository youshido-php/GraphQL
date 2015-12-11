<?php
/**
 * Date: 02.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Type\Config\Field\FieldConfig;

interface TypeConfigInterface
{

    /**
     * @param       $name
     * @param       $type
     * @param array $config
     *
     * @return TypeConfigInterface
     */
    public function addArgument($name, $type, $config = []);

    public function getArgument($name);

    public function removeArgument($name);

    public function hasArgument($name);

    public function getArguments();

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