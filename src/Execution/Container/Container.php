<?php

namespace Youshido\GraphQL\Execution\Container;

use Psr\Container\ContainerInterface;
use Youshido\GraphQL\Execution\Container\Exception\ContainerException;
use Youshido\GraphQL\Execution\Container\Exception\NotFoundException;

/**
 * Class Container
 */
class Container implements ContainerInterface
{
    /** @var array */
    private $values = [];

    /** @var array */
    private $instances = [];

    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get($id)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new NotFoundException(sprintf('Container item "%s" was not set', $id));
        }

        try {
            if (array_key_exists($id, $this->instances)) {
                return $this->instances[$id];
            }

            if (is_callable($this->values[$id]) || (is_object($this->values) && method_exists($this->values[$id], '__invoke'))) {
                $instance = $this->values[$id]();

                $this->instances[$id] = $instance;

                return $this->instances[$id];
            }

            return $this->values[$id];
        } catch (\Exception $exception) {
            throw new ContainerException($exception->getMessage());
        }
    }

    /**
     * @param string $id
     * @param mixed  $value
     */
    public function set($id, $value)
    {
        $this->values[$id] = $value;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->values[$id]);
    }

    /**
     * @return string[]
     */
    public function keys()
    {
        return array_keys($this->values);
    }
}
