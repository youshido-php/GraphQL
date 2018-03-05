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

namespace Youshido\GraphQL\Execution\Container;

interface ContainerInterface
{
    /**
     * @param string $id #Service
     *
     * @return mixed
     */
    public function get($id);

    /**
     * @param string $id
     * @param mixed  $value
     *
     * @return mixed
     */
    public function set($id, $value);

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function remove($id);

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function has($id);
}
