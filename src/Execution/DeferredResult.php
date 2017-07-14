<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Philipp Melab <philipp.melab@amazee.com>
 * created: 6/14/17 8:16 PM
 */

namespace Youshido\GraphQL\Execution;

/**
 * Helper class to recursively execute deferred resolvers in a query result.
 */
class DeferredResult
{

    protected $result;

    /** @var \Youshido\GraphQL\Execution\DeferredResolver[] */
    protected $deferredResolvers = [];

    public function __construct($result)
    {
        $this->result = $result;
    }


    /**
     * Evaluate deferred resolvers level by level.
     */
    public function resolve()
    {
        $this->scan($this->result);
        while ($this->deferredResolvers) {

            /** @var \Youshido\GraphQL\Execution\DeferredResolver[] */
            $resolved = [];

            // First resolve all queued resolvers.
            while ($deferredResolver = array_shift($this->deferredResolvers)) {
                $deferredResolver->resolve();
                $resolved[] = $deferredResolver;
            }
            // Scan the results for new resolvers in the second step.
            while ($resolver = array_shift($resolved)) {
                $this->scan($resolver->result);
            }
        }

        return static::unpack($this->result);
    }

    /**
     * Scan for deferred resolvers in a result tree and add them to the queue.
     *
     * @param mixed $data
     *   The result ree.
     */
    protected function scan($data)
    {
        if ($data instanceof DeferredResolver) {
            $this->deferredResolvers[] = $data;
        } elseif (is_array($data)) {
            foreach ($data as $value) {
                $this->scan($value);
            }
        }
    }

    /**
     * Unpack results stored inside deferred resolvers.
     *
     * @param mixed $result
     *   The result ree.
     *
     * @return mixed
     *   The unpacked result.
     */
    protected static function unpack($result)
    {
        while ($result instanceof DeferredResolver) {
            $result = $result->result;
        }

        if (is_array($result)) {
            foreach ($result as $key => $value) {
                $result[$key] = static::unpack($value);
            }
        }

        return $result;
    }

}
