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

    /**
     * @param string   $name
     * @param string   $type
     * @param bool     $nullable
     * @param bool     $isArray
     * @param Location $location
     * @param bool     $arrayElementNullable
     */
    public function __construct($name, $type, $nullable, $isArray, Location $location, $arrayElementNullable = true)
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
