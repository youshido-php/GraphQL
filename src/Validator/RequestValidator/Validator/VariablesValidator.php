<?php

namespace Youshido\GraphQL\Validator\RequestValidator\Validator;

use Youshido\GraphQL\Exception\Parser\InvalidRequestException;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\RequestValidator\RequestValidatorInterface;

/**
 * Class VariablesValidator
 */
class VariablesValidator implements RequestValidatorInterface
{
    /**
     * @param Request        $request
     * @param AbstractSchema $schema
     */
    public function validate(Request $request, AbstractSchema $schema)
    {
        $this->assertVariablesExists($request);
        $this->assertVariablesUsed($request);
    }

    private function assertVariablesExists(Request $request)
    {
        foreach ($request->getVariableReferences() as $variableReference) {
            if (!$variableReference->getVariable()) {
                throw new InvalidRequestException(sprintf('Variable "%s" not exists', $variableReference->getName()), $variableReference->getLocation());
            }
        }
    }

    private function assertVariablesUsed(Request $request)
    {
        foreach ($request->getQueryVariables() as $queryVariable) {
            if (!$queryVariable->isUsed()) {
                throw new InvalidRequestException(sprintf('Variable "%s" not used', $queryVariable->getName()), $queryVariable->getLocation());
            }
        }
    }
}
