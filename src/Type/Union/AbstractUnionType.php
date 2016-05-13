<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:12 AM
*/

namespace Youshido\GraphQL\Type\Union;


use Youshido\GraphQL\Config\Object\UnionTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractUnionType extends AbstractType
{

    use ConfigCallTrait, AutoNameTrait;

    protected $isFinal = false;

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name']  = $this->getName();
            $config['types'] = $this->getTypes();
        }

        $this->config = new UnionTypeConfig($config, $this, $this->isFinal);
    }

    /**
     * @return AbstractObjectType[]|AbstractScalarType[]
     */
    abstract public function getTypes();

    /**
     * @param $object object from resolve function
     *
     * @return AbstractObjectType|AbstractScalarType
     */
    abstract public function resolveType($object);

    public function getKind()
    {
        return TypeMap::KIND_UNION;
    }

    public function getNamedType()
    {
        return $this;
    }

    public function isValidValue($value)
    {
        return true;
    }

}
