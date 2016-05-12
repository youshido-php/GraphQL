<?php
/**
 * Date: 02.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Config;


use Youshido\GraphQL\Config\Field\FieldConfig;

interface TypeConfigInterface extends InputTypeConfigInterface
{

    /**
     * @param       $name
     * @param       $type
     * @param array $config
     *
     * @return TypeConfigInterface
     */
    public function addArgument($name, $type, $config = []);

    /**
     * @param $name
     *
     * @return FieldConfig
     */
    public function getArgument($name);

    public function removeArgument($name);

    public function hasArgument($name);

    public function getArguments();
}