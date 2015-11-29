<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 6:07 PM
*/

namespace Youshido\GraphQL\Validator\Rules;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class TypeValidationRule implements ValidationRuleInterface
{

    public function validate($data, $ruleInfo)
    {
        if (array_key_exists($ruleInfo, $this->getAllowedTypes())) {
            $typeObject = $this->getAllowedTypes()[$ruleInfo];
            return $typeObject->isValidValue($data);
        } elseif (is_object($data)) {
            $className = get_class($data);
            $className = substr($className, strrpos($className, '\\') + 1, -4);
            return ($className == $ruleInfo);
        } else {
            return false;
        }
    }

    /**
     * @return AbstractType[]
     */
    public function getAllowedTypes()
    {
        return [
            'int'    => new IntType(),
            'string' => new StringType(),
        ];
    }

}