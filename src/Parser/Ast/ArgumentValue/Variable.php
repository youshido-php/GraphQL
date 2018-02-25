<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;

use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class Variable extends AbstractAst implements ValueInterface
{

    /** @var  string */
    private $name;

    /** @var  mixed */
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
    private $defaultValue = null;

    /**
     * @param string   $name
     * @param string   $type
     * @param bool     $nullable
     * @param bool     $isArray
     * @param bool     $arrayElementNullable
     * @param Location $location
     */
    public function __construct($name, $type, $nullable, $isArray, $arrayElementNullable = true, Location $location)
    {
        parent::__construct($location);

        $this->name                 = $name;
        $this->type                 = $type;
        $this->isArray              = $isArray;
        $this->nullable             = $nullable;
        $this->arrayElementNullable = $arrayElementNullable;
    }

    /**
     * @return mixed
     *
     * @throws \LogicException
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
    public function setValue($value)
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
    public function setName($name)
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
    public function setTypeName($type)
    {
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function isArray()
    {
        return $this->isArray;
    }

    /**
     * @param boolean $isArray
     */
    public function setIsArray($isArray)
    {
        $this->isArray = $isArray;
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @param boolean $nullable
     */
    public function setNullable($nullable)
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
    public function setDefaultValue($defaultValue)
    {
        $this->hasDefaultValue = true;

        $this->defaultValue = $defaultValue;
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @param boolean $used
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
    public function setArrayElementNullable($arrayElementNullable)
    {
        $this->arrayElementNullable = $arrayElementNullable;
    }
}
