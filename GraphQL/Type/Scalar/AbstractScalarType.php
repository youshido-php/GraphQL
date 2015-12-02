<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:00 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Object\ScalarTypeConfig;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractScalarType extends AbstractType
{

    public function __construct($config = [])
    {
        if (!isset($config['name'])) {
            $config['name'] = $this->getName();
        }

        $this->config = new ScalarTypeConfig($config, $this);
    }

    public function getName()
    {
        $className = get_class($this);

        return substr($className, strrpos($className, '\\') + 1);
    }

    final public function getKind()
    {
        return TypeMap::KIND_SCALAR;
    }

    public function resolve($value = null, $args = [])
    {
        $callable = $this->config->getResolveFunction();

        return is_callable($callable) ? $callable($value, $args) : $this->serialize($value);
    }

}