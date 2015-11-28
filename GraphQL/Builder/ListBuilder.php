<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Builder;


use Youshido\GraphQL\Field;

class ListBuilder implements ListBuilderInterface
{

    protected $fields = [];

    /**
     * @inheritdoc
     */
    public function add($name, $type, $config = [])
    {
        if (is_string($type)) {
//            $type =
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