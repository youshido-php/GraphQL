<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:24 AM
*/

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

final class ObjectType extends AbstractObjectType
{

    protected $name = 'GenericObject';

    protected function build(TypeConfigInterface $config) { }

    function getName()
    {
        if (!$this->name) {
            throw new ConfigurationException("Type has to have a name");
        }

        return $this->name;
    }

    public function resolve($value = null, $args = [])
    {
        $callable = $this->config->getResolveFunction();

        return is_callable($callable) ? $callable($value, $args) : null;
    }
}