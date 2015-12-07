<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractEnumType extends AbstractType
{

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
            $config['values'] = $this->getValues();
        }

        $this->config = new EnumTypeConfig($config, $this);
    }

    /**
     * @return String predefined type kind
     */
    public function getKind()
    {
        return TypeMap::KIND_ENUM;
    }

    /**
     * @param $value mixed
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        return array_key_exists($value, $this->getValues());
    }

    abstract public function getValues();

    public function checkBuild()
    {
    }
}
