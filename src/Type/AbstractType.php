<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:05 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;

abstract class AbstractType implements TypeInterface
{

    /**
     * @var ObjectTypeConfig|InputObjectTypeConfig
     */
    protected $config;

    protected $isBuild = false;

    protected $name;

    /**
     * @return ObjectTypeConfig|InputObjectTypeConfig
     */
    public function getConfig()
    {
        $this->checkBuild();
        return $this->config;
    }

    public function checkBuild() {
        if (!$this->isBuild) {
            $this->isBuild = true;
            $this->build($this->config);
        }
    }

    protected function build(TypeConfigInterface $config) {}

    function getDescription()
    {
        return $this->getConfig()->get('description', '');
    }

    public function parseValue($value)
    {
        if (!$this->isValidValue($value)) {
            return null;
        }

        return $this->serialize($value);
    }

    public function serialize($value)
    {
        return $value;
    }

    public function __toString()
    {
        return $this->getName();
    }
}