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
use Youshido\GraphQL\Type\TypeMap;

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
            if (TypeMap::isTypeAllowed($ruleInfo)) {
                return TypeMap::getTypeObject($ruleInfo)->isValidValue($data);
            } elseif ($ruleInfo == 'object') {
                return is_object($data);
            }
        } else {
            return false;
        }
    }

}