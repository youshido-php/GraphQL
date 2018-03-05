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
/**
 * Date: 23.11.15.
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;

use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class Variable extends AbstractAst implements ValueInterface
{
    /** @var string */
    private $name;

    /** @var mixed */
    private $value;

    /** @var string */
    private $type;

    /** @var bool */
    private $nullable = false;

    /** @var bool */
    private $isArray = false;

    /** @var bool */
    private $used = false;

    /** @var bool */
    private $arrayElementNullable = true;

    /** @var bool */
    private $hasDefaultValue = false;

    /** @var mixed */
    private $defaultValue;

    /**
     * @param string   $name
     * @param string   $type
     * @param bool     $nullable
     * @param bool     $isArray
     * @param bool     $arrayElementNullable
     * @param Location $location
     */
    public function __construct($name, $type, $nullable, $isArray, $arrayElementNullable, Location $location)
    {
        parent::__construct($location);

        $this->name                 = $name;
        $this->type                 = $type;
        $this->isArray              = $isArray;
        $this->nullable             = $nullable;
        $this->arrayElementNullable = $arrayElementNullable;
    }

    /**
     * @throws \LogicException
     *
     * @return mixed
     */
    public function getValue()
    {
        if (null === $this->value) {
            if ($this->hasDefaultValue()) {
                return $this->defaultValue;
            }

            throw new \LogicException('Value is not set for variable "' . $this->name . '"');
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setTypeName($type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isArray()
    {
        return $this->isArray;
    }

    /**
     * @param bool $isArray
     */
    public function setIsArray($isArray): void
    {
        $this->isArray = $isArray;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     */
    public function setNullable($nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * @return bool
     */
    public function hasDefaultValue()
    {
        return $this->hasDefaultValue;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->hasDefaultValue = true;

        $this->defaultValue = $defaultValue;
    }

    /**
     * @return bool
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @param bool $used
     *
     * @return $this
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * @return bool
     */
    public function isArrayElementNullable()
    {
        return $this->arrayElementNullable;
    }

    /**
     * @param bool $arrayElementNullable
     */
    public function setArrayElementNullable($arrayElementNullable): void
    {
        $this->arrayElementNullable = $arrayElementNullable;
    }
}
