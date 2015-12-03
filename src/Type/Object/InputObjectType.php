<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 10:25 PM
*/

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Validator\Exception\ConfigurationException;

final class InputObjectType extends AbstractInputObjectType
{

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

    protected function getOutputType()
    {
        throw new ConfigurationException('You must define output type');
    }
}