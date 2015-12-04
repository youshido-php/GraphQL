<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config\Object;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Type\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class ObjectTypeConfig extends Config implements TypeConfigInterface
{

    use FieldsAwareTrait, ArgumentsAwareTrait;

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeMap::TYPE_STRING],
            'fields'      => ['type' => TypeMap::TYPE_ARRAY_OF_FIELDS],
            'args'        => ['type' => TypeMap::TYPE_ARRAY_OF_INPUTS],
            'resolve'     => ['type' => TypeMap::TYPE_FUNCTION],
        ];
    }

    public function getResolveFunction()
    {
        return $this->get('resolve');
    }

    protected function build()
    {
        $sourceFields = empty($this->data['fields']) ? [] : $this->data['fields'];
        foreach ($sourceFields as $fieldName => $fieldInfo) {
            if ($fieldInfo instanceof Field || $fieldInfo instanceof AbstractType) {
                $this->fields[$fieldName] = $fieldInfo;
                continue;
            };

            $this->addField($fieldName, $fieldInfo['type'], $fieldInfo);
        }

        $sourceArguments = empty($this->data['arguments']) ? [] : $this->data['arguments'];
        foreach ($sourceArguments as $argumentName => $argumentInfo) {
            $this->addArgument($argumentName, $argumentInfo['type'], $argumentInfo);
        }
    }

}