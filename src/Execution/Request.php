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

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Exception\Parser\InvalidRequestException;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;

class Request
{
    /** @var Query[] */
    private $queries = [];

    /** @var Fragment[] */
    private $fragments = [];

    /** @var Mutation[] */
    private $mutations = [];

    /** @var array */
    private $variables = [];

    /** @var VariableReference[] */
    private $variableReferences = [];

    /** @var array */
    private $queryVariables = [];

    /** @var array */
    private $fragmentReferences = [];

    public function __construct($data = [], $variables = [])
    {
        if (\array_key_exists('queries', $data)) {
            $this->addQueries($data['queries']);
        }

        if (\array_key_exists('mutations', $data)) {
            $this->addMutations($data['mutations']);
        }

        if (\array_key_exists('fragments', $data)) {
            $this->addFragments($data['fragments']);
        }

        if (\array_key_exists('fragmentReferences', $data)) {
            $this->addFragmentReferences($data['fragmentReferences']);
        }

        if (\array_key_exists('variables', $data)) {
            $this->addQueryVariables($data['variables']);
        }

        if (\array_key_exists('variableReferences', $data)) {
            foreach ($data['variableReferences'] as $ref) {
                if (!\array_key_exists($ref->getName(), $variables)) {
                    /** @var Variable $variable */
                    $variable = $ref->getVariable();

                    if ($variable->hasDefaultValue()) {
                        $variables[$variable->getName()] = $variable->getDefaultValue()->getValue();

                        continue;
                    }

                    throw new InvalidRequestException(\sprintf("Variable %s hasn't been submitted", $ref->getName()), $ref->getLocation());
                }
            }

            $this->addVariableReferences($data['variableReferences']);
        }

        $this->setVariables($variables);
    }

    public function addQueries($queries): void
    {
        foreach ($queries as $query) {
            $this->queries[] = $query;
        }
    }

    public function addMutations($mutations): void
    {
        foreach ($mutations as $mutation) {
            $this->mutations[] = $mutation;
        }
    }

    public function addQueryVariables($queryVariables): void
    {
        foreach ($queryVariables as $queryVariable) {
            $this->queryVariables[] = $queryVariable;
        }
    }

    public function addVariableReferences($variableReferences): void
    {
        foreach ($variableReferences as $variableReference) {
            $this->variableReferences[] = $variableReference;
        }
    }

    public function addFragmentReferences($fragmentReferences): void
    {
        foreach ($fragmentReferences as $fragmentReference) {
            $this->fragmentReferences[] = $fragmentReference;
        }
    }

    public function addFragments($fragments): void
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
        return \array_merge($this->mutations, $this->queries);
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

    public function addFragment(Fragment $fragment): void
    {
        $this->fragments[] = $fragment;
    }

    /**
     * @param $name
     *
     * @return Fragment|null
     */
    public function getFragment($name)
    {
        foreach ($this->fragments as $fragment) {
            if ($fragment->getName() === $name) {
                return $fragment;
            }
        }
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
        return (bool) \count($this->queries);
    }

    /**
     * @return bool
     */
    public function hasMutations()
    {
        return (bool) \count($this->mutations);
    }

    /**
     * @return bool
     */
    public function hasFragments()
    {
        return (bool) \count($this->fragments);
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
     *
     * @return $this
     */
    public function setVariables($variables)
    {
        if (!\is_array($variables)) {
            $variables = \json_decode($variables, true);
        }

        $this->variables = $variables;

        foreach ($this->variableReferences as $reference) {
            /* invalid request with no variable */
            if (!$reference->getVariable()) {
                continue;
            }
            $variableName = $reference->getVariable()->getName();

            /* no variable was set at the time */
            if (!\array_key_exists($variableName, $variables)) {
                continue;
            }

            $reference->getVariable()->setValue($variables[$variableName]);
            $reference->setValue($variables[$variableName]);
        }

        return $this;
    }

    public function getVariable($name)
    {
        return $this->hasVariable($name) ? $this->variables[$name] : null;
    }

    public function hasVariable($name)
    {
        return \array_key_exists($name, $this->variables);
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
    public function setQueryVariables($queryVariables): void
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
    public function setFragmentReferences($fragmentReferences): void
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
