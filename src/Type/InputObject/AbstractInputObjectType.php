<?php

namespace Youshido\GraphQL\Type\InputObject;

use Youshido\GraphQL\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Exception\ValidationException;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputObject;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class AbstractInputObjectType
 */
abstract class AbstractInputObjectType extends AbstractType
{
    use AutoNameTrait, FieldsAwareObjectTrait;

    protected $isBuilt = false;

    /**
     * AbstractInputObjectType constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = [
                'name' => $this->getName(),
            ];
        }
        $this->config = new InputObjectTypeConfig($config, $this);
    }

    /**
     * @return InputObjectTypeConfig
     */
    public function getConfig()
    {
        if (!$this->isBuilt) {
            $this->isBuilt = true;
            $this->build($this->config);
        }

        return $this->config;
    }

    /**
     * @param InputObjectTypeConfig $config
     */
    abstract public function build($config);

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        if ($value instanceof InputObject) {
            $value = $value->getValue();
        }

        if (empty($value)) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        $typeConfig     = $this->getConfig();
        $requiredFields = array_filter($typeConfig->getFields(), function (FieldInterface $field) {
            return $field->getType()->getKind() === TypeMap::KIND_NON_NULL;
        });

        foreach ($value as $valueKey => $valueItem) {
            if (!$typeConfig->hasField($valueKey)) {
                // Schema validation will generate the error message for us.
                return false;
            }

            $field = $typeConfig->getField($valueKey);
            if (!$field->getType()->isValidValue($valueItem)) {
                $error                     = $field->getType()->getValidationError($valueItem) ?: '(no details available)';
                $this->lastValidationError = sprintf('Not valid type for field "%s" in input type "%s": %s', $field->getName(), $this->getName(), $error);

                return false;
            }

            if (array_key_exists($valueKey, $requiredFields)) {
                unset($requiredFields[$valueKey]);
            }
        }
        if (count($requiredFields)) {
            $this->lastValidationError = sprintf('%s %s required on %s', implode(', ', array_keys($requiredFields)), count($requiredFields) > 1 ? 'are' : 'is', $typeConfig->getName());
        }

        return !(count($requiredFields) > 0);
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return TypeMap::KIND_INPUT_OBJECT;
    }

    /**
     * @return bool
     */
    public function isInputType()
    {
        return true;
    }

    /**
     * @param mixed $value
     *
     * @return array|null
     * @throws ValidationException
     */
    public function parseValue($value)
    {
        if (null === $value) {
            return null;
        }
        if ($value instanceof InputObject) {
            $value = $value->getValue();
        }

        $typeConfig = $this->getConfig();
        foreach ((array) $value as $valueKey => $item) {
            if ($item instanceof Variable) {
                $item = $item->getValue();
            }

            if (!($inputField = $typeConfig->getField($valueKey))) {
                throw new ValidationException(sprintf('Invalid field "%s" on %s', $valueKey, $typeConfig->getName()));
            }
            $value[$valueKey] = $inputField->getType()->parseValue($item);
        }

        return $value;
    }
}
