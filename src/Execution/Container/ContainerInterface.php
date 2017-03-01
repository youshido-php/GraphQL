<?php
namespace Youshido\GraphQL\Execution\Container;

interface ContainerInterface
{
    /**
     * @param string $id #Service
     * @return mixed
     */
    public function get($id);

    /**
     * @param string $id
     * @param mixed $value
     * @return mixed
     */
    public function set($id, $value);

    /**
     * @param string $id
     * @return mixed
     */
    public function remove($id);

    /**
     * @param string $id
     * @return mixed
     */
    public function has($id);

}