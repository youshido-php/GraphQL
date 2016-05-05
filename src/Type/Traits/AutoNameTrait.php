<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/4/16 9:18 PM
*/

namespace Youshido\GraphQL\Type\Traits;

/**
 * Class AutoNameTrait
 * @package Youshido\GraphQL\Type\Traits
 */
trait AutoNameTrait
{

    public function getName()
    {
        if (!empty($this->config)) {
            return $this->config->getName();
        }

        $className = get_called_class();
        if (substr($className, -4) == 'Type') {
            $className = substr($className, 0, -4);
        }
        if ($prevPos = strrpos($className, '\\')) {
            $className = substr($className, $prevPos + 1);
        }
        return $className;
    }

}
