<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/2/15 8:57 PM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ResolveException;

abstract class AbstractObjectType extends AbstractType
{

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
        }

        $this->config = new ObjectTypeConfig($config, $this);
    }


    abstract public function resolve($value = null, $args = []);

    abstract protected function build(TypeConfigInterface $config);

    public function checkBuild()
    {
        if (!$this->isBuild) {
            $this->isBuild = true;
            $this->build($this->config);
        }
    }


    public function parseValue($value)
    {
        return $value;
    }

    final public function serialize($value)
    {
        throw new ResolveException('You can not serialize object value directly');
    }

    public function getKind()
    {
        return TypeMap::KIND_OBJECT;
    }


    public function isValidValue($value)
    {
        return (get_class($value) == get_class($this));
    }

}