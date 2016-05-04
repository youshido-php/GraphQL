<?php
/**
 * Date: 04.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Traits;

use Youshido\GraphQL\Type\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Config\Object\ObjectTypeConfig;

/**
 * Class FinalTypesConfigTrait
 * @package Youshido\GraphQL\Type\Traits
 *
 * @method ObjectTypeConfig|InputObjectTypeConfig|FieldConfig getConfig()
 */
trait FinalTypesConfigTrait
{

    public function getName()
    {
        return $this->getConfig()->getName();
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        $callable = $this->getConfig()->getResolveFunction();

        return is_callable($callable) ? $callable($value, $args, $type) : $value;
    }

}