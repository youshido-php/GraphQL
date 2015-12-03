<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;


use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Parser\Value\Variable;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Validator\Exception\ResolveException;

class ResolveValidator implements ResolveValidatorInterface
{

    use ErrorContainerTrait;

    /**
     * @inheritdoc
     */
    public function validateArguments($queryType, $query, $request)
    {
        $requiredArguments = array_filter($queryType->getConfig()->getArguments(), function ($argument) {
            /** @var $argument Field */
            return $argument->getConfig()->isRequired();
        });

        foreach ($query->getArguments() as $argument) {
            if (!array_key_exists($argument->getName(), $queryType->getConfig()->getArguments())) {
                $this->addError(new ResolveException(sprintf('Unknown argument "%s" on field "%s".', $argument->getName(), $queryType->getName())));

                return false;
            }

            /** @var AbstractType $argumentType */
            $argumentType = $queryType->getConfig()->getArgument($argument->getName())->getType();
            if ($argument->getValue() instanceof Variable) {
                if ($request->hasVariable($argument->getName())) {
                    $argument->getValue()->setValue($request->getVariable($argument->getName()));
                } else {
                    $this->addError(new ResolveException(sprintf('Variable "%s" not exist for query "%s"', $argument->getName(), $queryType->getName())));

                    return false;
                }
            }

            if (!$argumentType->isValidValue($argumentType->parseValue($argument->getValue()->getValue()))) {
                $this->addError(new ResolveException(sprintf('Not valid type for argument "%s" in query "%s"', $argument->getName(), $queryType->getName())));

                return false;
            }

            if (array_key_exists($argument->getName(), $requiredArguments)) {
                unset($requiredArguments[$argument->getName()]);
            }
        }

        if (count($requiredArguments)) {
            $this->addError(new ResolveException(sprintf('Require "%s" arguments to query "%s"', implode(', ', array_keys($requiredArguments)), $query->getName())));

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function validateResolvedValue($value, $kind)
    {
        switch ($kind) {
            case TypeMap::KIND_OBJECT:
                return is_object($value);
            case TypeMap::KIND_LIST:
                return is_array($value);
        }

        return false;
    }

}