<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;


use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Value\Literal;
use Youshido\GraphQL\Type\Field\InputField;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Parser\Value\Variable;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Validator\Exception\ResolveException;

class ResolveValidator implements ResolveValidatorInterface
{

    use ErrorContainerTrait;

    /**
     * @inheritdoc
     */
    public function validateArguments($field, $query, $request)
    {
        $requiredArguments = array_filter($field->getConfig()->getArguments(), function (InputField $argument) {
            return $argument->getConfig()->get('required');
        });

        $withDefaultArguments = array_filter($field->getConfig()->getArguments(), function (InputField $argument) {
            return $argument->getConfig()->get('default') !== null;
        });

        foreach ($query->getArguments() as $argument) {
            if (!array_key_exists($argument->getName(), $field->getConfig()->getArguments())) {
                $this->addError(new ResolveException(sprintf('Unknown argument "%s" on field "%s".', $argument->getName(), $field->getName())));

                return false;
            }

            /** @var AbstractType $argumentType */
            $argumentType = $field->getConfig()->getArgument($argument->getName())->getType();
            if ($argument->getValue() instanceof Variable) {
                if ($request->hasVariable($argument->getValue()->getName())) {
                    $argument->getValue()->setValue($request->getVariable($argument->getValue()->getName()));
                } else {
                    $this->addError(new ResolveException(sprintf('Variable "%s" not exist for query "%s"', $argument->getName(), $field->getName())));

                    return false;
                }
            }

            if (!$argumentType->isValidValue($argumentType->parseValue($argument->getValue()->getValue()))) {
                $this->addError(new ResolveException(sprintf('Not valid type for argument "%s" in query "%s"', $argument->getName(), $field->getName())));

                return false;
            }

            if (array_key_exists($argument->getName(), $requiredArguments)) {
                unset($requiredArguments[$argument->getName()]);
            }
            if (array_key_exists($argument->getName(), $withDefaultArguments)) {
                unset($withDefaultArguments[$argument->getName()]);
            }
        }

        if (count($requiredArguments)) {
            $this->addError(new ResolveException(sprintf('Require "%s" arguments to query "%s"', implode(', ', array_keys($requiredArguments)), $query->getName())));

            return false;
        }

        if (count($withDefaultArguments)) {
            foreach ($withDefaultArguments as $name => $argument) {
                $query->addArgument(new Argument($name, new Literal( $argument->getConfig()->get('default'))));
            }
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
            case TypeMap::KIND_INPUT_OBJECT:
            case TypeMap::KIND_INTERFACE:
                return is_object($value) || is_null($value) || is_array($value);
            case TypeMap::KIND_LIST:
                return is_null($value)|| is_array($value) || (is_object($value) && in_array('IteratorAggregate', class_implements($value)));
        }

        return false;
    }

}