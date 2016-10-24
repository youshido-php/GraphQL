<?php
/**
 * Date: 10/24/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\RequestValidator;


use Youshido\GraphQL\Execution\Request;

class RequestValidator implements RequestValidatorInterface
{

    public function validate(Request $request)
    {

    }

    private function assertAllVariablesUsed(Request $request)
    {
        if ($this->variablesInfo && count($this->variablesInfo) != count($this->variableTypeUsage)) {

        }
    }
}
