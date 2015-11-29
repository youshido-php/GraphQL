<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 2:25 AM
*/

namespace Youshido\GraphQL\Validator;


use Youshido\GraphQL\Validator\Rules\TypeValidationRule;
use Youshido\GraphQL\Validator\Rules\ValidationRuleInterface;
use Youshido\GraphQL\Validator\Exception\ValidationException;

class Validator implements ValidatorInterface
{

    protected $rules = [];

    /** @var \Exception[] */
    protected $errors = [];

    protected $contextObject;

    /** @var ValidationRuleInterface[] */
    protected $validationRules = [];

    public function __construct($contextObject = null)
    {
        $this->contextObject = $contextObject;
        $this->initializeRules();
    }

    public function validate($data, $rules = [])
    {
        foreach ($rules as $fieldName => $fieldRules) {
            /** Custom validation of 'required' property */
            if (array_key_exists('required', $fieldRules)) {
                unset($fieldRules['required']);
                if (!array_key_exists($fieldName, $data)) {
                    $this->addError(new ValidationException('Field \'' . $fieldName . '\' of ' . $this->getContextName() . ' is required'));
                }
            } elseif (!in_array($fieldName, $data)) {
                continue;
            }

            /** Validation of all other rules*/
            foreach ($fieldRules as $ruleName => $ruleInfo) {
                if (!array_key_exists($ruleName, $this->validationRules)) {
                    $this->addError(new ValidationException('Field \'' . $fieldName . '\' has invalid rule \'' . $ruleInfo . '\''));
                    continue;
                }

                if (!$this->validationRules[$ruleName]->validate($data[$fieldName], $ruleInfo)) {
                    $this->addError(
                        new ValidationException('Field \'' . $fieldName . '\' of ' . $this->getContextName()
                                                . ' expected to be ' . $ruleName . ': \'' . (string)$ruleInfo . '\', but got: ' . $data[$fieldName]));
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
            $class = substr($class, strrpos($class, '\\')+1);
            return $class;
        } else {
            return $this->contextObject;
        }
    }

    public function setRules($rules)
    {
        $this->rules = (array)$rules;
        return $this;
    }

    public function setRuleForField($fieldName, $rule)
    {
        $this->rules[$fieldName] = $rule;
        return $this;
    }

    public function addError(\Exception $exception)
    {
        $this->errors[] = $exception;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function isValid()
    {
        return !$this->hasErrors();
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsArray()
    {
        $errors = [];

        foreach ($this->errors as $error) {
            $errors[] = is_object($error) ? $error->getMessage() : $error;
        }

        return $errors;
    }

    public function clearErrors()
    {
        $this->errors = [];
        return $this;
    }

}