<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/2/15 9:00 PM
*/

namespace Youshido\GraphQL\Type\InputObject;


use Youshido\GraphQL\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractInputObjectType extends AbstractType
{

    use AutoNameTrait, ConfigAwareTrait;

    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = [
                'name' => $this->getName()
            ];
        }
        $this->config = new InputObjectTypeConfig($config, $this);
        $this->build($this->config);
    }

    /**
     * @param InputObjectTypeConfig $config
     * @return mixed
     */
    abstract public function build($config);

    public function isValidValue($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $typeConfig     = $this->getConfig();
        $requiredFields = array_filter($typeConfig->getFields(), function (FieldInterface $field) {
            return $field->getType()->getKind() == TypeMap::KIND_NON_NULL;
        });

        foreach ($value as $valueKey => $valueItem) {
            if (!$typeConfig->hasField($valueKey) || !$typeConfig->getField($valueKey)->getType()->isValidValue($valueItem)) {
                return false;
            }

            if (array_key_exists($valueKey, $requiredFields)) {
                unset($requiredFields[$valueKey]);
            }
        }

        return !(count($requiredFields) > 0);
    }

    public function getKind()
    {
        return TypeMap::KIND_INPUT_OBJECT;
    }

}
