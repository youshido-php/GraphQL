<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/2/15 8:57 PM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ResolveException;

/**
 * Class AbstractObjectType
 * @package Youshido\GraphQL\Type\Object
 *
 * @method bool hasFields()
 * @method bool hasField($field)
 * @method Field getField($field)
 * @method $this addField($name, $type, $config = [])
 * @method $this addFields($fields)
 */
abstract class AbstractObjectType extends AbstractType
{
    use AutoNameTrait, ConfigCallTrait;

    protected $isBuild = false;

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config['name']       = $this->getName();
            $config['interfaces'] = $this->getInterfaces();
        }

        $this->config = new ObjectTypeConfig($config, $this);
    }

    final public function serialize($value)
    {
        throw new ResolveException('You can not serialize object value directly');
    }

    public function getKind()
    {
        return TypeMap::KIND_OBJECT;
    }

    public function getType()
    {
        return $this->getConfigValue('type', $this);
    }

    public function getNamedType()
    {
        return $this;
    }

    public function getConfig()
    {
        if (!$this->isBuild) {
            $this->isBuild = true;
            $this->build($this->config);
        }
        return parent::getConfig();
    }

    /**
     * @param TypeConfigInterface $config
     * @return mixed
     */
    abstract public function build($config);

    /**
     * @return AbstractInterfaceType[]
     */
    public function getInterfaces()
    {
        return $this->getConfigValue('interfaces', []);
    }

    public function isValidValue($value)
    {
        return (get_class($value) == get_class($this));
    }

}
