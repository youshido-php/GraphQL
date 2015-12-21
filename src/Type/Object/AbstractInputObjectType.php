<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/2/15 9:00 PM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\Config\InputTypeConfigInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractInputObjectType extends AbstractType
{
    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config) && (get_class($this) != 'Youshido\GraphQL\Type\Object\InputObjectType')) {
            $config['name']   = $this->getName();
            $config['output'] = $this->getOutputType();
        }

        $this->config = new InputObjectTypeConfig($config, $this);
    }

    abstract protected function getOutputType();

    public function resolve($value = null, $args = [])
    {

    }

    protected function build(InputTypeConfigInterface $config)
    {

    }

    public function isValidValue($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $requiredFields = array_filter($this->getConfig()->getFields(), function (Field $field) {
            return $field->getConfig()->get('required');
        });

        foreach ($value as $valueKey => $valueItem) {
            if (!$this->getConfig()->hasField($valueKey) || !$this->getConfig()->getField($valueKey)->getType()->isValidValue($valueItem)) {
                return false;
            }

            if (array_key_exists($valueKey, $requiredFields)) {
                unset($requiredFields[$valueKey]);
            }
        }

        return !(count($requiredFields) > 0);
    }

    public function checkBuild()
    {
        if (!$this->isBuild) {
            $this->isBuild = true;
            $this->build($this->config);
        }
    }

    public function getKind()
    {
        return TypeMap::KIND_INPUT_OBJECT;
    }

}