<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


class Field
{
    /** @var string */
    private $name;

    /** @var null|string */
    private $alias = null;

    /** @var Argument[] */
    protected $arguments;

    public function __construct($name, $alias = null, $arguments = [])
    {
        $this->name  = $name;
        $this->alias = $alias;
        $this->arguments = $arguments;
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
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param null|string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function hasArguments()
    {
        return (bool)count($this->arguments);
    }

    /**
     * @return Argument[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function addArgument(Argument $argument)
    {
        $this->arguments[$argument->getName()] = $argument;
    }

    public function getKeyValueArguments()
    {
        $arguments = [];

        foreach ($this->getArguments() as $argument) {
            $arguments[$argument->getName()] = $argument->getValue()->getValue();
        }

        return $arguments;
    }
}