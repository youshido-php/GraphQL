<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 1:30 AM
*/

namespace Youshido\GraphQL\Config\Field;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class FieldConfig extends AbstractConfig
{

    use ArgumentsAwareTrait {
        getArguments as traitGetArguments;
        getArgument as traitGetArgument;
    }

    public function getRules()
    {
        return [
            'name'              => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'type'              => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'args'              => ['type' => TypeService::TYPE_ARRAY],
            'description'       => ['type' => TypeService::TYPE_STRING],
            'resolve'           => ['type' => TypeService::TYPE_FUNCTION],
            'isDeprecated'      => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
        ];
    }

    protected function build()
    {
        $this->buildArguments();
    }

    public function issetResolve()
    {
        $resolveFunction = $this->getResolveFunction();

        return $resolveFunction && is_callable($resolveFunction);
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
     * @return \Youshido\GraphQL\Field\InputField[]
     */
    public function getArguments()
    {
        return $this->traitGetArguments() ?: $this->getType()->getConfig()->getArguments();
    }

}
