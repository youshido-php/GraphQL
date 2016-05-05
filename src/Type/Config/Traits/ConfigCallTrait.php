<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/1/16 11:19 AM
*/

namespace Youshido\GraphQL\Type\Config\Traits;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\Field\Field;

/**
 * Class ConfigCallTrait
 * @package Youshido\GraphQL\Type\Config\Traits
 *
 * @method string getDescription()
 * @method string getKind()
 * @method AbstractType getNamedType()
 * @method $this setType($type)
 * @method Field getField($field)
 * @method bool hasField($field)
 * @method bool hasFields()
 * @method bool isDeprecated()
 * @method string getDeprecationReason()
 * @property Config $config
 *
 */
trait ConfigCallTrait
{
    public function __call($method, $arguments)
    {
        $propertyName     = false;
        $passAlongMethods = ['hasField', 'addField', 'removeField', 'getFields', 'hasFields', 'getField', 'getNamedType'];

        if (in_array($method, $passAlongMethods)) {
            $this->checkBuild();

            return call_user_func_array([$this->config, $method], $arguments);
        } elseif (substr($method, 0, 3) == 'get') {
            $propertyName = lcfirst(substr($method, 3));
        } elseif (substr($method, 0, 3) == 'set') {
            $propertyName = lcfirst(substr($method, 3));
            $this->config->set($propertyName, $arguments[0]);

            return $this;
        } elseif (substr($method, 0, 2) == 'is') {
            $propertyName = lcfirst(substr($method, 2));
        }
        if (in_array($propertyName, ['name', 'description', 'deprecationReason', 'isDeprecated', 'field', 'type'])) {
            return $this->config->get($propertyName);
        }

        throw new \Exception('Call to undefined method ' . $method);
    }

}
