<?php

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Directive\DirectiveInterface;

class SchemaDirectiveCollection
{
    /** @var DirectiveInterface[] */
    private $directives = [];

    /**
     * @param DirectiveInterface[] $directives
     */
    public function addMany(array $directives)
    {
        foreach ($directives as $directive) {
            $this->add($directive);
        }
    }

    /**
     * @param DirectiveInterface $directive
     */
    public function add(DirectiveInterface $directive)
    {
        $this->directives[$directive->getName()] = $directive;
    }

    /**
     * @param string $name
     *
     * @return DirectiveInterface|null
     */
    public function get($name)
    {
        if (isset($this->directives[$name])) {
            return $this->directives[$name];
        }

        return null;
    }

    /**
     * @return DirectiveInterface[]
     */
    public function all()
    {
        return $this->directives;
    }
}
