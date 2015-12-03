<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\ListType;


use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class ListType extends AbstractListType
{

    public function getItem()
    {
        throw new \Exception('You must define type of List Item');
    }

    function getName()
    {
        if (!$this->name) {
            throw new ConfigurationException("Type has to have a name");
        }

        return $this->name;
    }

    public function resolve($value = null, $args = [])
    {
        $callable = $this->config->getResolveFunction();

        return is_callable($callable) ? $callable($value, $args) : null;
    }
}