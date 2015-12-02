<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:05 PM
*/

namespace Youshido\GraphQL\Type\Config\Traits;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

trait FieldsAwareTrait
{
    protected $fields = [];


    public function addField($name, $type, $config = [])
    {
        if (is_string($type)) {
            if (!TypeMap::isTypeAllowed($type)) {
                throw new ConfigurationException('You can\'t pass ' . $type . ' as a string type.');
            }

            $type = TypeMap::getScalarTypeObject($type);
        }

        $config['name'] = $name;
        $config['type'] = $type;
        $field          = new Field($config);

        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * @param $name
     * @return Field
     */
    public function getField($name)
    {
        return $this->hasField($name) ? $this->fields[$name] : null;
    }

    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
    }

    public function getFields()
    {
        return $this->fields;
    }
}