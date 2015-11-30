<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL;


use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;

class Request
{

    private $queries = [];
    private $fragments = [];
    private $mutations = [];
    private $variables = [];

    public function __construct($data = [])
    {
        if (array_key_exists('queries', $data)) {
            $this->addQueries($data['queries']);
        }

        if (array_key_exists('mutations', $data)) {
            $this->addMutations($data['mutations']);
        }

        if (array_key_exists('fragments', $data)) {
            $this->addFragments($data['fragments']);
        }
    }

    /**
     * @return Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    public function addQueries($queries)
    {
        foreach ($queries as $query) {
            $this->queries[] = $query;
        }
    }

    /**
     * @return Fragment[]
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    public function addFragment($fragment)
    {
        $this->fragments[] = $fragment;
    }

    /**
     * @return Mutation[]
     */
    public function getMutations()
    {
        return $this->mutations;
    }

    public function addMutations($mutations)
    {
        foreach ($mutations as $mutation) {
            $this->mutations[] = $mutation;
        }
    }

    public function addFragments($fragments)
    {
        foreach ($fragments as $fragment) {
            $this->fragments[] = $fragment;
        }
    }

    /**
     * @return bool
     */
    public function hasFragments()
    {
        return (bool)count($this->fragments);
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    public function hasVariable($name)
    {
        return array_key_exists($name, $this->variables);
    }

    public function getVariable($name)
    {
        return $this->hasVariable($name) ? $this->variables[$name] : null;
    }
}