<?php

namespace Youshido\GraphQL\Validator\RequestValidator\Validator;

use Youshido\GraphQL\Exception\Parser\InvalidRequestException;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\RequestValidator\RequestValidatorInterface;

/**
 * Class FragmentsAreUsedValidator
 */
class FragmentsValidator implements RequestValidatorInterface
{
    /**
     * @param Request        $request
     * @param AbstractSchema $schema
     */
    public function validate(Request $request, AbstractSchema $schema)
    {
        $this->assertFragmentReferencesValid($request);
        $this->assertFragmentsUsed($request);
    }

    private function assertFragmentsUsed(Request $request)
    {
        foreach ($request->getFragmentReferences() as $fragmentReference) {
            $request->getFragment($fragmentReference->getName())->setUsed(true);
        }

        foreach ($request->getFragments() as $fragment) {
            if (!$fragment->isUsed()) {
                throw new InvalidRequestException(sprintf('Fragment "%s" not used', $fragment->getName()), $fragment->getLocation());
            }
        }
    }

    private function assertFragmentReferencesValid(Request $request)
    {
        foreach ($request->getFragmentReferences() as $fragmentReference) {
            if (!$request->getFragment($fragmentReference->getName())) {
                throw new InvalidRequestException(sprintf('Fragment "%s" not defined in query', $fragmentReference->getName()), $fragmentReference->getLocation());
            }
        }
    }
}
