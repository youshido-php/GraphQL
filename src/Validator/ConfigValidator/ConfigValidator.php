<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11/28/15 2:25 AM
 */

namespace Youshido\GraphQL\Validator\ConfigValidator;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Exception\ValidationException;
use Youshido\GraphQL\Validator\ConfigValidator\Rules\TypeValidationRule;
use Youshido\GraphQL\Validator\ConfigValidator\Rules\ValidationRuleInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

class ConfigValidator implements ConfigValidatorInterface
{
    use ErrorContainerTrait;

    protected $rules = [];

    protected $extraFieldsAllowed = false;

    /** @var ValidationRuleInterface[] */
    protected $validationRules = [];

    /** @var ConfigValidator */
    protected static $instance;

    private function __construct()
    {
        $this->initializeRules();
    }

    /**
     * @return ConfigValidator
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        self::$instance->clearErrors();

        return self::$instance;
    }

    public function assertValidConfig(AbstractConfig $config): void
    {
        if (!$this->isValidConfig($config)) {
            throw new ConfigurationException('Config is not valid for ' . ($config->getContextObject() ? \get_class($config->getContextObject()) : null) . "\n" . \implode("\n", $this->getErrorsArray(false)));
        }
    }

    public function isValidConfig(AbstractConfig $config)
    {
        return $this->validate($config->getData(), $this->getConfigFinalRules($config), $config->isExtraFieldsAllowed());
    }

    public function validate($data, $rules = [], $extraFieldsAllowed = null)
    {
        if (null !== $extraFieldsAllowed) {
            $this->setExtraFieldsAllowed($extraFieldsAllowed);
        }

        $processedFields = [];

        foreach ($rules as $fieldName => $fieldRules) {
            $processedFields[] = $fieldName;

            if (!isset($data[$fieldName])) {
                continue;
            }
            /* Custom validation of 'required' property */
            if (isset($fieldRules['required'])) {
                unset($fieldRules['required']);

                if (!isset($data[$fieldName])) {
                    $this->addError(new ValidationException(\sprintf('Field "%s" is required', $fieldName)));

                    continue;
                }
            }

            if (!empty($fieldRules['final'])) {
                unset($fieldRules['final']);
            }

            /* Validation of all other rules*/
            foreach ($fieldRules as $ruleName => $ruleInfo) {
                if (!isset($this->validationRules[$ruleName])) {
                    $this->addError(new ValidationException(\sprintf('Field "%s" has invalid rule "%s"', $fieldName, $ruleInfo)));

                    continue;
                }

                if (!$this->validationRules[$ruleName]->validate($data[$fieldName], $ruleInfo)) {
                    $this->addError(new ValidationException(\sprintf('Field "%s" expected to be "%s" but got "%s"', $fieldName, $ruleName, \gettype($data[$fieldName]))));
                }
            }
        }

        if (!$this->isExtraFieldsAllowed()) {
            foreach (\array_keys($data) as $fieldName) {
                if (!\in_array($fieldName, $processedFields, true)) {
                    $this->addError(new ValidationException(\sprintf('Field "%s" is not expected', $fieldName)));
                }
            }
        }

        return $this->isValid();
    }

    public function addRule($name, ValidationRuleInterface $rule): void
    {
        $this->validationRules[$name] = $rule;
    }

    public function isValid()
    {
        return !$this->hasErrors();
    }

    /**
     * @return bool
     */
    public function isExtraFieldsAllowed()
    {
        return $this->extraFieldsAllowed;
    }

    /**
     * @param bool $extraFieldsAllowed
     *
     * @return ConfigValidator
     */
    public function setExtraFieldsAllowed($extraFieldsAllowed)
    {
        $this->extraFieldsAllowed = $extraFieldsAllowed;

        return $this;
    }

    protected function getConfigFinalRules(AbstractConfig $config)
    {
        $rules = $config->getRules();

        if ($config->isFinalClass()) {
            foreach ($rules as $name => $info) {
                if (!empty($info['final'])) {
                    $rules[$name]['required'] = true;
                }
            }
        }

        return $rules;
    }

    protected function initializeRules(): void
    {
        $this->validationRules['type'] = new TypeValidationRule($this);
    }
}
