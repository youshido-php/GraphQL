<?php

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Exception\ConfigurationException;

/**
 * Class NonNullType
 */
final class NonNullType extends AbstractType implements CompositeTypeInterface
{
    use ConfigAwareTrait;

    private $_typeOf;

    /**
     * NonNullType constructor.
     *
     * @param AbstractType|string $fieldType
     *
     * @throws ConfigurationException
     */
    public function __construct($fieldType)
    {
        if (!TypeService::isGraphQLType($fieldType)) {
            throw new ConfigurationException('NonNullType accepts only GraphpQL Types as argument');
        }
        if (TypeService::isScalarType($fieldType)) {
            $fieldType = TypeFactory::getScalarType($fieldType);
        }

        $this->_typeOf = $fieldType;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return null;
    }

    /**
     * @return string
     */
    final public function getKind()
    {
        return TypeMap::KIND_NON_NULL;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function resolve($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        if ($value === null) {
            return false;
        }

        return $this->getNullableType()->isValidValue($value);
    }

    /**
     * @return bool
     */
    public function isCompositeType()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isInputType()
    {
        return true;
    }

    /**
     * @return string|AbstractType
     */
    public function getNamedType()
    {
        return $this->getTypeOf();
    }

    /**
     * @return string|AbstractType
     */
    public function getNullableType()
    {
        return $this->getTypeOf();
    }

    /**
     * @return string|AbstractType
     */
    public function getTypeOf()
    {
        return $this->_typeOf;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        return $this->getNullableType()->parseValue($value);
    }

    /**
     * @param mixed $value
     *
     * @return mixed|string
     */
    public function getValidationError($value = null)
    {
        if ($value === null) {
            return 'Field must not be NULL';
        }

        return $this->getNullableType()->getValidationError($value);
    }
}
