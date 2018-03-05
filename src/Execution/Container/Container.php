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
 * created: 9/22/16 7:00 PM
 */

namespace Youshido\GraphQL\Execution\Container;

class Container implements ContainerInterface
{
    private $keyset = [];

    private $values = [];

    private $services = [];

    /**
     * @param $id
     *
     * @throws \Exception if there was no value set under specified id
     *
     * @return mixed
     */
    public function get($id)
    {
        $this->assertIdentifierSet($id);

        if (isset($this->services['id'])) {
            return $this->services['id']($this);
        }

        return $this->values[$id];
    }

    public function set($id, $value)
    {
        $this->values[$id] = $value;
        $this->keyset[$id] = true;

        return $this;
    }

    public function remove($id): void
    {
        $this->assertIdentifierSet($id);

        if (\array_key_exists($id, $this->values)) {
            unset($this->values[$id]);
        }

        if (\array_key_exists($id, $this->services)) {
            unset($this->services[$id]);
        }
    }

    public function has($id)
    {
        return isset($this->keyset[$id]);
    }

    protected function setAsService($id, $service): void
    {
        if (!\is_object($service)) {
            throw new \RuntimeException(\sprintf('Service %s has to be an object', $id));
        }

        $this->services[$id] = $service;

        if (isset($this->values[$id])) {
            unset($this->values[$id]);
        }
        $this->keyset[$id] = true;
    }

    private function assertIdentifierSet($id): void
    {
        if (!$this->has($id)) {
            throw new \RuntimeException(\sprintf('Container item "%s" was not set', $id));
        }
    }
}
