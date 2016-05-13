<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:18 AM
*/

namespace Youshido\GraphQL\Config\Object;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class UnionTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareTrait, ArgumentsAwareTrait;

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'types'       => ['type' => TypeService::TYPE_FIELDS_LIST_CONFIG],
            'description' => ['type' => TypeMap::TYPE_STRING],
            'resolve'     => ['type' => TypeService::TYPE_FUNCTION],
            'resolveType' => ['type' => TypeService::TYPE_FUNCTION] //todo: must be required
        ];
    }

    public function resolveType($object)
    {
        $callable = $this->get('resolveType');

        if ($callable && is_callable($callable)) {
            return call_user_func_array($callable, [$object]);
        }

        return $this->contextObject->resolveType($object);
    }

    protected function build()
    {
        $this->buildFields();
    }
}
