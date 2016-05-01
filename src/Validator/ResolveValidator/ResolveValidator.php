<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;


use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Value\Literal;
use Youshido\GraphQL\Type\Field\InputField;
use Youshido\GraphQL\Type\Object\InputObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Parser\Value\Variable;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Validator\Exception\ResolveException;

class ResolveValidator implements ResolveValidatorInterface, ErrorContainerInterface
{

    use ErrorContainerTrait;

    /**
     * @param $objectType InputObjectType|ObjectType
     * @param $field      Mutation|Query
     * @return null
     */
    public function checkFieldExist($objectType, $field)
    {
        if (!$objectType->getConfig()->hasField($field->getName())) {
//            if ($objectType->getKind() == TypeMap::KIND_LIST) {
//                $name = $objectType->getConfig()->getItem()->getName();
//            } else {
//                $name = $objectType->getName();
//            }

            $this->addError(new ResolveException(sprintf('Field "%s" not found in type "%s"', $field->getName(), $objectType->getNamedType()->getName())));

            return false;
        }

        return true;
    }

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
                /** @var Variable $variable */
                $variable = $argument->getValue();

                if($variable->getType() !== $argumentType->getName()) {
                    $this->addError(new ResolveException(sprintf('Invalid variable "%s" type, allowed type is "%s"', $variable->getName(), $argumentType->getName())));

                    return false;
                }

                if ($request->hasVariable($variable->getName())) {
                    $variable->setValue($request->getVariable($variable->getName()));
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
                $query->addArgument(new Argument($name, new Literal($argument->getConfig()->get('default'))));
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
                return is_null($value) || is_array($value) || (is_object($value) && in_array('IteratorAggregate', class_implements($value)));
        }

        return false;
    }

}