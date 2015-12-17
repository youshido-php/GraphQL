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
     * @return InputTypeConfigInterface
     */
    public function addArgument($name, $type, $config = []);

    public function getArgument($name);

    public function removeArgument($name);

    public function hasArgument($name);

    public function getArguments();

}