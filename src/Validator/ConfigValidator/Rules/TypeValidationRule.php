<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 6:07 PM
*/

namespace Youshido\GraphQL\Validator\ConfigValidator\Rules;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Field\InputField;
use Youshido\GraphQL\Type\Object\AbstractInputObjectType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class TypeValidationRule implements ValidationRuleInterface
{

    public function validate($data, $ruleInfo)
    {
        if (is_object($ruleInfo)) {
            $className = get_class($data);
            $className = substr($className, strrpos($className, '\\') + 1, -4);

            return ($className == $ruleInfo);
        } elseif (is_string($ruleInfo)) {
            /** @todo need to be completely rewritten */
            $ruleInfo = strtolower($ruleInfo);

            switch ($ruleInfo) {
                case TypeMap::TYPE_ANY:
                    return true;
                    break;

                case TypeMap::TYPE_ANY_OBJECT:
                    return is_object($data);
                    break;

                case TypeMap::TYPE_OBJECT_TYPE:
                    return $data instanceof AbstractObjectType;
                    break;

                case TypeMap::TYPE_OBJECT_INPUT_TYPE:
                    return $data instanceof AbstractInputObjectType;
                    break;

                case TypeMap::TYPE_FUNCTION:
                    return is_callable($data);
                    break;

                case TypeMap::TYPE_BOOLEAN:
                    return is_bool($data);
                    break;

                case TypeMap::TYPE_ARRAY:
                    return is_array($data);
                    break;

                case TypeMap::TYPE_ARRAY_OF_FIELDS:
                    return $this->isArrayOfFields($data);
                    break;

                case TypeMap::TYPE_ARRAY_OF_INPUTS:
                    return $this->isArrayOfInputs($data);
                    break;

                case TypeMap::TYPE_ANY_INPUT:
                    return TypeMap::isInputType($data);
                    break;

                default:
                    if (TypeMap::isScalarType($ruleInfo)) {
                        return TypeMap::getScalarTypeObject($ruleInfo)->isValidValue($data);
                    }
                    break;
            }
        } else {
            return false;
        }
    }

    private function isArrayOfFields($data)
    {
        if (!is_array($data)) return false;

        foreach ($data as $name => $item) {
            if (!$this->isField($item, $name)) return false;
        }
        return true;
    }

    private function isArrayOfInputs($data)
    {
        if (!is_array($data)) return false;

        foreach ($data as $name => $item) {
            if (!$this->isInputField($item, $name)) return false;
        }
        return true;
    }

    private function isField($data, $name = null)
    {
        if (is_object($data)) {
            return !!(($data instanceof Field) || ($data instanceof AbstractType));
        }

        try {
            /** @todo need to change it to optimize performance */
            if (empty($data['name'])) $data['name'] = $name;

            $config = new FieldConfig($data);
            return $config->isValid();
        } catch (ConfigurationException $e) {
            /** just need to return false in this case */
        }

        return false;
    }

    private function isInputField($data, $name = null)
    {
        if (is_object($data)) {
            return !!(($data instanceof InputField) || ($data instanceof AbstractInputObjectType));
        }
        try {
            /** @todo need to change it to optimize performance */
            if (empty($data['name'])) $data['name'] = $name;

            $config = new InputFieldConfig($data);
            return $config->isValid();
        } catch (ConfigurationException $e) {
            /** just need to return false in this case */
        }

        return false;
    }

}