<?php

namespace Youshido\GraphQL\Parser\Ast;

/**
 * Trait AstArgumentsTrait
 */
trait AstArgumentsTrait
{
    /** @var Argument[] */
    protected $arguments;

    /** @var null|mixed */
    private $argumentsCache;

    /**
     * @return bool
     */
    public function hasArguments()
    {
        return (bool) count($this->arguments);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasArgument($name)
    {
        return array_key_exists($name, $this->arguments);
    }

    /**
     * @return Argument[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param string $name
     *
     * @return null|Argument
     */
    public function getArgument($name)
    {
        $argument = null;
        if (isset($this->arguments[$name])) {
            $argument = $this->arguments[$name];
        }

        return $argument;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function getArgumentValue($name)
    {
        $argument = $this->getArgument($name);

        return $argument ? $argument->getValue()->getValue() : null;
    }

    /**
     * @param $arguments Argument[]
     */
    public function setArguments(array $arguments)
    {
        $this->arguments      = [];
        $this->argumentsCache = null;

        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    /**
     * @param Argument $argument
     */
    public function addArgument(Argument $argument)
    {
        $this->arguments[$argument->getName()] = $argument;
    }

    /**
     * @return array|mixed|null
     */
    public function getKeyValueArguments()
    {
        if ($this->argumentsCache !== null) {
            return $this->argumentsCache;
        }

        $this->argumentsCache = [];

        foreach ($this->getArguments() as $argument) {
            $this->argumentsCache[$argument->getName()] = $argument->getValue()->getValue();
        }

        return $this->argumentsCache;
    }
}
