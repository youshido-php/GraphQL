<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 2:25 AM
*/

namespace Youshido\GraphQL\Validator\ConfigValidator;


use Youshido\GraphQL\Validator\ConfigValidator\Rules\TypeValidationRule;
use Youshido\GraphQL\Validator\ConfigValidator\Rules\ValidationRuleInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Validator\Exception\ValidationException;

class ConfigValidator implements ConfigValidatorInterface
{

    use ErrorContainerTrait;

    protected $rules = [];

    protected $contextObject;

    protected $extraFieldsAllowed = false;

    /** @var ValidationRuleInterface[] */
    protected $validationRules = [];

    public function __construct($contextObject = null)
    {
        $this->contextObject = $contextObject;
        $this->initializeRules();
    }

    public function validate($data, $rules = [], $extraFieldsAllowed = null)
    {
        if ($extraFieldsAllowed !== null) $this->setExtraFieldsAllowed($extraFieldsAllowed);

        $processedFields = [];
        foreach ($rules as $fieldName => $fieldRules) {
            $processedFields[] = $fieldName;

            /** Custom validation of 'required' property */
            if (array_key_exists('required', $fieldRules)) {
                unset($fieldRules['required']);

                if (!array_key_exists($fieldName, $data)) {
                    $this->addError(new ValidationException('Field \'' . $fieldName . '\' of ' . $this->getContextName() . ' is required'));

                    continue;
                }
            } elseif (!array_key_exists($fieldName, $data)) {
                continue;
            }
            if (!empty($fieldRules['final'])) unset($fieldRules['final']);

            /** Validation of all other rules*/
            foreach ($fieldRules as $ruleName => $ruleInfo) {
                if (!array_key_exists($ruleName, $this->validationRules)) {
                    $this->addError(new ValidationException('Field \'' . $fieldName . '\' has invalid rule \'' . $ruleInfo . '\''));

                    continue;
                }

                if (!$this->validationRules[$ruleName]->validate($data[$fieldName], $ruleInfo)) {
                    $this->addError(
                        new ValidationException('Field \'' . $fieldName . '\' of ' . $this->getContextName()
                            . ' expected to be ' . $ruleName . ': \'' . (string)$ruleInfo . '\', but got: ' . gettype($data[$fieldName])));
                }
            }
        }

        if (!$this->isExtraFieldsAllowed()) {
            foreach (array_keys($data) as $fieldName) {
                if (!in_array($fieldName, $processedFields)) {
                    $this->addError(
                        new ValidationException('Field \'' . $fieldName . '\' is not expected in ' . $this->getContextName()));

                }
            }
        }

        return $this->isValid();
    }

    protected function initializeRules()
    {
        $this->validationRules['type'] = new TypeValidationRule();
    }

    /**
     * @return string
     */
    protected function getContextName()
    {
        if (is_object($this->contextObject)) {
            $class = get_class($this->contextObject);
            $class = substr($class, strrpos($class, '\\') + 1);

            return $class;
        } else {
            return $this->contextObject ? $this->contextObject : '(context)';
        }
    }

    public function isValid()
    {
        return !$this->hasErrors();
    }


    /**
     * @return boolean
     */
    public function isExtraFieldsAllowed()
    {
        return $this->extraFieldsAllowed;
    }

    /**
     * @param boolean $extraFieldsAllowed
     *
     * @return ConfigValidator
     */
    public function setExtraFieldsAllowed($extraFieldsAllowed)
    {
        $this->extraFieldsAllowed = $extraFieldsAllowed;

        return $this;
    }

}
