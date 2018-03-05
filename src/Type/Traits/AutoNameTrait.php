<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/4/16 9:18 PM
*/

namespace Youshido\GraphQL\Type\Traits;
use Youshido\GraphQL\Field\FieldInterface;

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

        if ($prevPos = strrpos($className, '\\')) {
            return substr($className, $prevPos + 1);
        }

        if (substr($className, -5) == 'Field') {
            return lcfirst(substr($className, 0, -5));
        }

        if (substr($className, -4) == 'Type') {
            return substr($className, 0, -4);
        }

        if ($this instanceof FieldInterface) {
            return lcfirst($className);
        }


        return $className;
    }

}
