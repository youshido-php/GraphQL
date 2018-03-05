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
 * created: 5/10/16 11:32 PM
 */

namespace Youshido\GraphQL\Relay;

use Youshido\GraphQL\Relay\Fetcher\FetcherInterface;
use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;

class NodeInterfaceType extends AbstractInterfaceType
{
    /** @var FetcherInterface */ //todo: maybe there are better solution

    protected $fetcher;

    public function getName()
    {
        return 'NodeInterface';
    }

    public function build($config): void
    {
        $config->addField(new GlobalIdField('NodeInterface'));
    }

    public function resolveType($object)
    {
        if ($this->fetcher) {
            return $this->fetcher->resolveType($object);
        }
    }

    /**
     * @return FetcherInterface
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * @param FetcherInterface $fetcher
     *
     * @return NodeInterfaceType
     */
    public function setFetcher($fetcher)
    {
        $this->fetcher = $fetcher;

        return $this;
    }
}
