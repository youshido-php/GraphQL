<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;

use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
use Youshido\GraphQL\Validator\Exception\ResolveException;

class ResolveValidator implements ResolveValidatorInterface
{

    /** @var  ExecutionContextInterface */
    protected $executionContext;

    public function __construct(ExecutionContextInterface $executionContext)
    {
        $this->executionContext = $executionContext;
    }

    /**
     * @param AbstractObjectType      $objectType
     * @param Mutation|Query|AstField $field
     * @return null
     */
    public function objectHasField($objectType, $field)
    {
        if (!($objectType instanceof AbstractObjectType) || !$objectType->hasField($field->getName())) {
            $this->executionContext->addError(new ResolveException(sprintf('Field "%s" not found in type "%s"', $field->getName(), $objectType->getNamedType()->getName())));

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function validateArguments(AbstractField $field, $query, Request $request)
    {
        if (!count($field->getArguments())) return true;

        $requiredArguments = array_filter($field->getArguments(), function (InputField $argument) {
            return $argument->getType()->getKind() == TypeMap::KIND_NON_NULL;
        });

        $withDefaultArguments = array_filter($field->getArguments(), function (InputField $argument) {
            return $argument->getConfig()->get('default') !== null;
        });

        foreach ($query->getArguments() as $argument) {
            if (!$field->hasArgument($argument->getName())) {
                $this->executionContext->addError(new ResolveException(sprintf('Unknown argument "%s" on field "%s"', $argument->getName(), $field->getName())));

                return false;
            }

            /** @var AbstractType $argumentType */
            $argumentType = $field->getArgument($argument->getName())->getType()->getNullableType()->getNamedType();
            if ($argument->getValue() instanceof Variable) {
                /** @var Variable $variable */
                $variable = $argument->getValue();

                //todo: here validate argument

                if ($variable->getTypeName() !== $argumentType->getName()) {
                    $this->executionContext->addError(new ResolveException(sprintf('Invalid variable "%s" type, allowed type is "%s"', $variable->getName(), $argumentType->getName())));

                    return false;
                }

                /** @var Variable $requestVariable */
                $requestVariable = $request->getVariable($variable->getName());
                if (!$requestVariable) {
                    $this->executionContext->addError(new ResolveException(sprintf('Variable "%s" does not exist for query "%s"', $argument->getName(), $field->getName())));

                    return false;
                }
                $variable->setValue($requestVariable);

            }

            if (!$argumentType->isValidValue($argumentType->parseValue($argument->getValue()->getValue()))) {
                $this->executionContext->addError(new ResolveException(sprintf('Not valid type for argument "%s" in query "%s"', $argument->getName(), $field->getName())));

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
            $this->executionContext->addError(new ResolveException(sprintf('Require "%s" arguments to query "%s"', implode(', ', array_keys($requiredArguments)), $query->getName())));

            return false;
        }

        if (count($withDefaultArguments)) {
            foreach ($withDefaultArguments as $name => $argument) {
                $query->addArgument(new Argument($name, new Literal($argument->getConfig()->get('default'))));
            }
        }

        return true;
    }

    public function assertTypeImplementsInterface(AbstractType $type, AbstractInterfaceType $interface)
    {
        if (!$interface->isValidValue($type)) {
            throw new ResolveException('Type ' . $type->getName() . ' does not implement ' . $interface->getName());
        }
    }

    public function assertTypeInUnionTypes(AbstractType $type, AbstractUnionType $unionType)
    {
        $unionTypes = $unionType->getTypes();
        $valid      = false;
        if (empty($unionTypes)) return false;

        foreach ($unionTypes as $unionType) {
            if ($unionType->getName() == $type->getName()) {
                $valid = true;

                break;
            }
        }

        if (!$valid) {
            throw new ResolveException('Type ' . $type->getName() . ' not exist in types of ' . $unionType->getName());
        }
    }

    /**
     * @param Fragment          $fragment
     * @param FragmentReference $fragmentReference
     * @param AbstractType      $queryType
     *
     * @throws \Exception
     */
    public function assertValidFragmentForField(Fragment $fragment, FragmentReference $fragmentReference, AbstractType $queryType)
    {
        if ($fragment->getModel() !== $queryType->getName()) {
            throw new ResolveException(sprintf('Fragment reference "%s" not found on model "%s"', $fragmentReference->getName(), $queryType->getName()));
        }
    }

    public function isValidValueForField(AbstractField $field, $value)
    {
        $fieldType = $field->getType();
        if ($fieldType->getKind() == TypeMap::KIND_NON_NULL && is_null($value)) {
            $this->executionContext->addError(new ResolveException(sprintf('Cannot return null for non-nullable field %s', $field->getName())));
            return null;
        } else {
            $fieldType = $this->resolveTypeIfAbstract($fieldType->getNullableType(), $value);
        }

        if (!is_null($value) && !$fieldType->isValidValue($value)) {
            $this->executionContext->addError(new ResolveException(sprintf('Not valid value for %s field %s', $fieldType->getNullableType()->getKind(), $field->getName())));
            return null;
        }
        return true;
    }

    public function resolveTypeIfAbstract(AbstractType $type, $resolvedValue)
    {
        if (TypeService::isAbstractType($type)) {
            /** @var AbstractInterfaceType $type */
            $resolvedType = $type->resolveType($resolvedValue);

            if (!$resolvedType) {
                $this->executionContext->addError(new \Exception('Cannot resolve type'));
                return $type;
            }
            if ($type instanceof AbstractInterfaceType) {
                $this->assertTypeImplementsInterface($resolvedType, $type);
            } else {
                /** @var AbstractUnionType $type */
                $this->assertTypeInUnionTypes($resolvedType, $type);
            }

            return $resolvedType;
        }

        return $type;
    }
    /**
     * @return ExecutionContextInterface
     */
    public function getExecutionContext()
    {
        return $this->executionContext;
    }

    /**
     * @param ExecutionContextInterface $executionContext
     */
    public function setExecutionContext($executionContext)
    {
        $this->executionContext = $executionContext;
    }

}
