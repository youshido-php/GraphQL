<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 12/2/15 8:57 PM
 */

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class AbstractObjectType.
 */
abstract class AbstractObjectType extends AbstractType
{
    use AutoNameTrait, FieldsArgumentsAwareObjectTrait;

    protected $isBuilt = false;

    /**
     * ObjectType constructor.
     *
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

    public function getConfig()
    {
        if (!$this->isBuilt) {
            $this->isBuilt = true;
            $this->build($this->config);
        }

        return $this->config;
    }

    final public function serialize($value): void
    {
        throw new \InvalidArgumentException('You can not serialize object value directly');
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

    /**
     * @param ObjectTypeConfig $config
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
        return \is_array($value) || null === $value || \is_object($value);
    }
}
