<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Type\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class ObjectTypeConfig extends Config
{

    use FieldsAwareTrait, ArgumentsAwareTrait;

    public function getResolveFunction()
    {
        return empty($this->data['resolve']) ? null : $this->data['resolve'];
    }

    public function getRules()
    {
        return [
            'name'      => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'fields'    => [],
            'arguments' => [],
            'resolve'   => [],
        ];
    }

    protected function setupType()
    {
        $sourceFields = empty($this->data['fields']) ? [] : $this->data['fields'];
        foreach ($sourceFields as $fieldName => $fieldInfo) {
            if ($fieldInfo instanceof Field || $fieldInfo instanceof ObjectType) {
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