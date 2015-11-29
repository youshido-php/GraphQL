<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Field;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ObjectTypeConfig extends Config
{

    protected function setupType()
    {
        foreach ($this->getFields() as $fieldName => $fieldInfo) {
            if ($fieldInfo instanceof Field || $fieldInfo instanceof ObjectType) continue;

            $fieldObject = new Field();
            // @todo it's just for test
            $typeClassName = 'Youshido\\GraphQL\\Type\\Scalar\\' . ucfirst($fieldInfo['type']) . 'Type';
            $fieldObject->setName($fieldName)->setConfig($fieldInfo)->setType(new $typeClassName());
            $this->data['fields'][$fieldName] = $fieldObject;
        }

    }


    public function getName()
    {
        return $this->data['name'];
    }

    public function getFields()
    {
        return empty($this->data['fields']) ? [] : $this->data['fields'];
    }

    public function getResolveFunction()
    {
        return empty($this->data['resolve']) ? null : $this->data['resolve'];
    }

    public function getRules()
    {
        return [
            'name'    => ['type' => 'string', 'required' => true],
            'fields'  => [],
            'resolve' => [],
        ];
    }


}