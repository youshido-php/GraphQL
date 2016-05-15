<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5:12 PM 5/14/16
 */

namespace Youshido\GraphQL\Type\Traits;


trait FieldsArgumentsAwareObjectTrait
{
    use FieldsAwareObjectTrait;

    public function addArguments($argumentsList)
    {
        return $this->getConfig()->addArguments($argumentsList);
    }

    public function addArgument($argument, $ArgumentInfo = null)
    {
        return $this->getConfig()->addArgument($argument, $ArgumentInfo);
    }

    public function getArguments()
    {
        return $this->getConfig()->getArguments();
    }

    public function getArgument($argumentName)
    {
        return $this->getConfig()->getArgument($argumentName);
    }

    public function hasArgument($argumentName)
    {
        return $this->getConfig()->hasArgument($argumentName);
    }

    public function hasArguments()
    {
        return $this->getConfig()->hasArguments();
    }
}