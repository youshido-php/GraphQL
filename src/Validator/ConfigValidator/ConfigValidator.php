<?php

namespace Youshido\GraphQL\Validator\ConfigValidator;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Directive\DirectiveLocation;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Exception\GraphQLException;
use Youshido\GraphQL\Exception\ValidationException;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\InputFieldInterface;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeKind;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

/**
 * Class ConfigValidator
 */
class ConfigValidator implements ErrorContainerInterface
{
    use ErrorContainerTrait;

    /** @var  ConfigValidator */
    protected static $instance;

    /**
     * ConfigValidator constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return ConfigValidator
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        self::$instance->clearErrors();

        return self::$instance;
    }

    /**
     * @param AbstractConfig $config
     *
     * @return bool
     */
    public function validate(AbstractConfig $config)
    {
        $requiredRules = array_filter($config->getRules(), function ($ruleInfo) {
            return isset($ruleInfo['required']) && $ruleInfo['required'];
        });

        $rules = $config->getRules();
        foreach ($config->getData() as $key => $value) {
            if (!isset($rules[$key])) {
                $this->addError(new ValidationException(sprintf('Config parameter "%s" is not expected', $key)));

                continue;
            }

            $rule = $rules[$key];
            if (isset($rule['required']) && $rule['required']) {
                unset($requiredRules[$key]);
            }

            $this->validateRule($key, $rule, $value);
        }

        if ($requiredRules) {
            foreach ($requiredRules as $key => $rule) {
                $this->addError(new ValidationException(sprintf('Config parameter "%s" is required', $key)));
            }
        }

        return $this->isValid();
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !$this->hasErrors();
    }

    /**
     * @return array
     */
    public function getErrorsArray()
    {
        return array_map(function (GraphQLException $exception) {
            return $exception->getMessage();
        }, $this->getErrors());
    }

    protected function validateRule($key, array $rule, $data)
    {
        if (!isset($rule['type'])) {
            throw new ConfigurationException(sprintf('Bad configuration for parameter "%s"', $key));
        }

        $isArray    = isset($rule['array']) && $rule['array'];
        $isRequired = isset($rule['required']) && $rule['required'];

        if (empty($data) && $isRequired) {
            $this->addError(new ValidationException(sprintf('Config parameter "%s" is required', $key)));

            return;
        }

        if ($isArray) {
            if (!is_array($data)) {
                $this->addError(new ValidationException(sprintf('Config parameter "%s" must be array', $key)));

                return;
            }

            foreach ($data as $item) {
                if (!$this->isValidType($rule['type'], $item)) {
                    $this->addError(new ValidationException(sprintf('Config parameter "%s" has bad type. Expected type is array of "%s"', $key, $rule['type'])));

                    break;
                }
            }

            return;
        }

        if (!$this->isValidType($rule['type'], $data)) {
            $this->addError(new ValidationException(sprintf('Config parameter "%s" has bad type. Expected type is "%s"', $key, $rule['type'])));
        }
    }

    /**
     * @param $type
     * @param $data
     *
     * @return bool
     */
    protected function isValidType($type, $data)
    {
        switch ($type) {
            case PropertyType::TYPE_STRING:
                return is_string($data);

            case PropertyType::TYPE_LOCATION:
                return is_string($data) && in_array($data, DirectiveLocation::$locations, false);

            case PropertyType::TYPE_CALLABLE:
                return is_callable($data);

            case PropertyType::TYPE_INT:
                return is_int($data);

            case PropertyType::TYPE_COST:
                return is_int($data) || is_callable($data);

            case PropertyType::TYPE_BOOLEAN:
                return is_bool($data);

            case PropertyType::TYPE_DIRECTIVE:
                return is_object($data) && $data instanceof DirectiveInterface;

            case PropertyType::TYPE_INTERFACE:
                return is_object($data) && $data instanceof TypeInterface && $data->getKind() === TypeKind::KIND_INTERFACE;

            case PropertyType::TYPE_OBJECT_TYPE:
                return is_object($data) && $data instanceof TypeInterface && $data->getKind() === TypeKind::KIND_OBJECT;

            case PropertyType::TYPE_FIELD:
                return is_object($data) && $data instanceof FieldInterface;

            case PropertyType::TYPE_INPUT_FIELD:
                return is_object($data) && $data instanceof InputFieldInterface;

            case PropertyType::TYPE_GRAPHQL_TYPE:
                return is_object($data) && $data instanceof TypeInterface;

            case PropertyType::TYPE_ENUM_VALUES:
                if (!is_array($data) || empty($data)) {
                    return false;
                }

                foreach ($data as $item) {
                    if (!is_array($item) || !array_key_exists('name', $item) || !is_string($item['name']) || !preg_match('/^[_a-zA-Z][_a-zA-Z0-9]*$/', $item['name'])) {
                        return false;
                    }

                    if (!array_key_exists('value', $item)) {
                        return false;
                    }
                }

                return true;


            case PropertyType::TYPE_INPUT_TYPE:
                if (is_object($data) && $data instanceof TypeInterface) {
                    $namedType = $data->getNullableType()->getNamedType()->getNullableType();

                    return in_array($namedType->getKind(), [TypeKind::KIND_SCALAR, TypeKind::KIND_LIST, TypeKind::KIND_ENUM, TypeKind::KIND_INPUT_OBJECT], false);
                }

                return false;

            case PropertyType::TYPE_ANY:
                return true;

            default:
                $this->addError(new ConfigurationException(sprintf('Config parameter type "%s" not supported', $type)));
        }

        return false;
    }
}
