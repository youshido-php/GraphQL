<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 1:30 AM
*/

namespace Youshido\GraphQL\Type\Config\Field;

use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

class FieldConfig extends Config
{

    use ArgumentsAwareTrait {
        getArguments as traitGetArguments;
        getArgument as traitGetArgument;
    }

    public function getRules()
    {
        return [
            'name'              => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'type'              => ['type' => TypeMap::TYPE_ANY, 'required' => true],
            'args'              => ['type' => TypeMap::TYPE_ARRAY],
            'required'          => ['type' => TypeMap::TYPE_BOOLEAN],
            'description'       => ['type' => TypeMap::TYPE_STRING],
            'resolve'           => ['type' => TypeMap::TYPE_FUNCTION],
            'isDeprecated'      => ['type' => TypeMap::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeMap::TYPE_STRING],
        ];
    }

    protected function build()
    {
        $this->buildArguments();
    }

    public function resolve($value = null, $args = [])
    {
        $resolveFunction = $this->get('resolve', null);

        if ($resolveFunction && is_callable($resolveFunction)) {
            return $resolveFunction($value, $args);
        }

        return $this->getType()->resolve($value, $args);
    }

    /**
     * @return TypeInterface|AbstractObjectType
     */
    public function getType()
    {
        return $this->data['type'];
    }

    public function getArgument($name)
    {
        return $this->traitGetArgument($name) ?: $this->getType()->getConfig()->getArgument($name);
    }

    /**
     * @return \Youshido\GraphQL\Type\Field\InputField[]
     */
    public function getArguments()
    {
        return $this->traitGetArguments() ?: $this->getType()->getConfig()->getArguments();
    }

}