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
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2/5/17 11:31 AM
 */

namespace Youshido\GraphQL\Parser\Ast;

trait AstArgumentsTrait
{
    /** @var Argument[] */
    protected $arguments;

    private $argumentsCache;

    public function hasArguments()
    {
        return (bool) \count($this->arguments);
    }

    public function hasArgument($name)
    {
        return \array_key_exists($name, $this->arguments);
    }

    /**
     * @return Argument[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $name
     *
     * @return Argument|null
     */
    public function getArgument($name)
    {
        $argument = null;

        if (isset($this->arguments[$name])) {
            $argument = $this->arguments[$name];
        }

        return $argument;
    }

    public function getArgumentValue($name)
    {
        $argument = $this->getArgument($name);

        return $argument ? $argument->getValue()->getValue() : null;
    }

    /**
     * @param $arguments Argument[]
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments      = [];
        $this->argumentsCache = null;

        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    public function addArgument(Argument $argument): void
    {
        $this->arguments[$argument->getName()] = $argument;
    }

    public function getKeyValueArguments()
    {
        if (null !== $this->argumentsCache) {
            return $this->argumentsCache;
        }

        $this->argumentsCache = [];

        foreach ($this->getArguments() as $argument) {
            $this->argumentsCache[$argument->getName()] = $argument->getValue()->getValue();
        }

        return $this->argumentsCache;
    }
}
