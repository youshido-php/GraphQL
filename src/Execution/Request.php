<?php

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Exception\Parser\InvalidRequestException;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\ParseResult;

/**
 * Class Request
 */
class Request
{
    /** @var  Query[] */
    private $queries = [];

    /** @var Fragment[] */
    private $fragments = [];

    /** @var Mutation[] */
    private $mutations = [];

    /** @var array */
    private $variables = [];

    /** @var VariableReference[] */
    private $variableReferences = [];

    /** @var  array */
    private $queryVariables = [];

    /** @var array */
    private $fragmentReferences = [];

    /**
     * Request constructor.
     *
     * @param ParseResult  $parseResult
     * @param array|string $variables
     *
     * @throws InvalidRequestException
     */
    public function __construct(ParseResult $parseResult = null, $variables = [])
    {
        if ($parseResult) {
            $this->addQueries($parseResult->getQueries());
            $this->addMutations($parseResult->getMutations());
            $this->addFragments($parseResult->getFragments());
            $this->addFragmentReferences($parseResult->getFragmentReferences());
            $this->addQueryVariables($parseResult->getVariables());
            $this->addVariableReferences($parseResult->getVariableReferences());

            foreach ($parseResult->getVariableReferences() as $ref) {
                if (!array_key_exists($ref->getName(), $variables)) {
                    /** @var Variable $variable */
                    $variable = $ref->getVariable();
                    if ($variable->hasDefaultValue()) {
                        $variables[$variable->getName()] = $variable->getDefaultValue()->getValue();
                        continue;
                    }

                    throw new InvalidRequestException(sprintf("Variable %s hasn't been submitted", $ref->getName()), $ref->getLocation());
                }
            }
        }

        $this->setVariables($variables);
    }

    /**
     * @param Query[] $queries
     */
    public function addQueries($queries)
    {
        foreach ($queries as $query) {
            $this->queries[] = $query;
        }
    }

    /**
     * @param Mutation[] $mutations
     */
    public function addMutations($mutations)
    {
        foreach ($mutations as $mutation) {
            $this->mutations[] = $mutation;
        }
    }

    /**
     * @param Variable[] $queryVariables
     */
    public function addQueryVariables($queryVariables)
    {
        foreach ($queryVariables as $queryVariable) {
            $this->queryVariables[] = $queryVariable;
        }
    }

    /**
     * @param VariableReference[] $variableReferences
     */
    public function addVariableReferences($variableReferences)
    {
        foreach ($variableReferences as $variableReference) {
            $this->variableReferences[] = $variableReference;
        }
    }

    /**
     * @param FragmentReference[] $fragmentReferences
     */
    public function addFragmentReferences($fragmentReferences)
    {
        foreach ($fragmentReferences as $fragmentReference) {
            $this->fragmentReferences[] = $fragmentReference;
        }
    }

    /**
     * @param Fragment[] $fragments
     */
    public function addFragments($fragments)
    {
        foreach ($fragments as $fragment) {
            $this->addFragment($fragment);
        }
    }

    /**
     * @return Query[]
     */
    public function getAllOperations()
    {
        return array_merge($this->mutations, $this->queries);
    }

    /**
     * @return Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @return Fragment[]
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    /**
     * @param Fragment $fragment
     */
    public function addFragment(Fragment $fragment)
    {
        $this->fragments[] = $fragment;
    }

    /**
     * @param string $name
     *
     * @return null|Fragment
     */
    public function getFragment($name)
    {
        foreach ($this->fragments as $fragment) {
            if ($fragment->getName() === $name) {
                return $fragment;
            }
        }

        return null;
    }

    /**
     * @return Mutation[]
     */
    public function getMutations()
    {
        return $this->mutations;
    }

    /**
     * @return bool
     */
    public function hasQueries()
    {
        return (bool) count($this->queries);
    }

    /**
     * @return bool
     */
    public function hasMutations()
    {
        return (bool) count($this->mutations);
    }

    /**
     * @return bool
     */
    public function hasFragments()
    {
        return (bool) count($this->fragments);
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array|string $variables
     */
    public function setVariables($variables)
    {
        if (!is_array($variables)) {
            $variables = json_decode($variables, true);
        }

        $this->variables = $variables;
        foreach ($this->variableReferences as $reference) {
            /** invalid request with no variable */
            if (!$reference->getVariable()) {
                continue;
            }

            $variableName = $reference->getVariable()->getName();
            /** no variable was set at the time */
            if (!isset($this->variables[$variableName])) {
                continue;
            }

            $reference->getVariable()->setValue($variables[$variableName]);
            $reference->setValue($variables[$variableName]);
        }
    }

    /**
     * @param string $name
     *
     * @return array|mixed|null
     */
    public function getVariable($name)
    {
        return $this->hasVariable($name) ? $this->variables[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasVariable($name)
    {
        return isset($this->variables[$name]);
    }

    /**
     * @return array|Variable[]
     */
    public function getQueryVariables()
    {
        return $this->queryVariables;
    }

    /**
     * @param array $queryVariables
     */
    public function setQueryVariables($queryVariables)
    {
        $this->queryVariables = $queryVariables;
    }

    /**
     * @return array|FragmentReference[]
     */
    public function getFragmentReferences()
    {
        return $this->fragmentReferences;
    }

    /**
     * @param array $fragmentReferences
     */
    public function setFragmentReferences($fragmentReferences)
    {
        $this->fragmentReferences = $fragmentReferences;
    }

    /**
     * @return array|VariableReference[]
     */
    public function getVariableReferences()
    {
        return $this->variableReferences;
    }
}
