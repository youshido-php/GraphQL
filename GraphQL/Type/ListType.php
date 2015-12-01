<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Config\ListTypeConfig;

class ListType extends AbstractType
{

    public function __construct($config = [])
    {
        if (empty($config) && (get_class($this) != 'Youshido\GraphQL\Type\Object\ListType')) {
            $config['name'] = $this->getName();
        }

        $this->config = new ListTypeConfig($config, $this);
        $this->name   = $this->config->getName();
    }

    public function isValidValue($value)
    {
        return is_array($value);
    }

    final public function getKind()
    {
        return TypeMap::KIND_LIST;
    }

}