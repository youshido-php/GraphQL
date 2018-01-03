<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Field\InputFieldInterface;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class InputObjectTypeConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string $description)
 * @method string|null getDescription()
 */
class InputObjectTypeConfig extends AbstractConfig
{
    use FieldsAwareConfigTrait;

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => PropertyType::TYPE_STRING, 'required' => true],
            'fields'      => ['type' => PropertyType::TYPE_INPUT_FIELD, 'required' => true, 'array' => true],
            'description' => ['type' => PropertyType::TYPE_STRING],
        ];
    }

    /**
     * Configure class properties
     */
    protected function build()
    {
        $this->buildFields();
    }

    protected function isFieldInstance($field)
    {
        return $field instanceof InputFieldInterface;
    }

    protected function createField($field, $info = null)
    {
        return new InputField($this->buildFieldConfig($field, $info));
    }
}
