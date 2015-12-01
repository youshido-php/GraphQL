<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/29/15 12:24 AM
*/
namespace Youshido\GraphQL\Builder;


use Youshido\GraphQL\Field;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class ListBuilder implements ListBuilderInterface
{

    protected $fields = [];

    /**
     * @inheritdoc
     */
    public function add($name, $type, $config = [])
    {
        if (is_string($type)) {
            if (!TypeMap::isTypeAllowed($type)) throw new ConfigurationException('You can\'t pass ' . $type . ' as a string type.');
            $type = TypeMap::getScalarTypeObject($type);
        }

        $field = new Field();
        $field
            ->setName($name)
            ->setType($type)
            ->setConfig($config);

        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->fields[$name];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function has($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->fields;
    }
}