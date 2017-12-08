<?php

namespace Youshido\GraphQL\Parser;

use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Directive;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;

/**
 * Class ParseResult
 */
class ParseResult
{
    /** @var Query[] */
    private $queries = [];

    /** @var Mutation[] */
    private $mutations = [];

    /** @var Fragment[] */
    private $fragments = [];

    /** @var FragmentReference[] */
    private $fragmentReferences = [];

    /** @var Variable[] */
    private $variables = [];

    /** @var VariableReference[] */
    private $variableReferences = [];

    /** @var  Directive[] */
    private $directives = [];

    /**
     * @return Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param Query[] $queries
     */
    public function setQueries($queries)
    {
        $this->queries = $queries;
    }

    /**
     * @param Query $query
     */
    public function addQuery(Query $query)
    {
        $this->queries[] = $query;
    }

    /**
     * @return Mutation[]
     */
    public function getMutations()
    {
        return $this->mutations;
    }

    /**
     * @param Mutation[] $mutations
     */
    public function setMutations($mutations)
    {
        $this->mutations = $mutations;
    }

    /**
     * @param Mutation $mutation
     */
    public function addMutation(Mutation $mutation)
    {
        $this->mutations[] = $mutation;
    }

    /**
     * @return Fragment[]
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    /**
     * @param Fragment[] $fragments
     */
    public function setFragments($fragments)
    {
        $this->fragments = $fragments;
    }

    /**
     * @param Fragment $fragment
     */
    public function addFragment(Fragment $fragment)
    {
        $this->fragments[] = $fragment;
    }

    /**
     * @return FragmentReference[]
     */
    public function getFragmentReferences()
    {
        return $this->fragmentReferences;
    }

    /**
     * @param FragmentReference[] $fragmentReferences
     */
    public function setFragmentReferences($fragmentReferences)
    {
        $this->fragmentReferences = $fragmentReferences;
    }

    /**
     * @param FragmentReference $fragmentReference
     */
    public function addFragmentReference(FragmentReference $fragmentReference)
    {
        $this->fragmentReferences[] = $fragmentReference;
    }

    /**
     * @return Variable[]
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param Variable[] $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param Variable $variable
     */
    public function addVariable(Variable $variable)
    {
        $this->variables[] = $variable;
    }

    /**
     * @param string $name
     *
     * @return null|Variable
     */
    public function getVariable($name)
    {
        foreach ($this->variables as $variable) {
            if ($variable->getName() === $name) {
                return $variable;
            }
        }

        return null;
    }

    /**
     * @return VariableReference[]
     */
    public function getVariableReferences()
    {
        return $this->variableReferences;
    }

    /**
     * @param VariableReference[] $variableReferences
     */
    public function setVariableReferences($variableReferences)
    {
        $this->variableReferences = $variableReferences;
    }

    /**
     * @param VariableReference $variableReference
     */
    public function addVariableReference(VariableReference $variableReference)
    {
        $this->variableReferences[] = $variableReference;
    }

    /**
     * @return Directive[]
     */
    public function getDirectives()
    {
        return $this->directives;
    }

    /**
     * @param Directive[] $directives
     */
    public function setDirectives($directives)
    {
        $this->directives = $directives;
    }
}
