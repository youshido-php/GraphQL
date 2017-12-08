<?php

namespace Youshido\GraphQL\Relay;

use Youshido\GraphQL\Relay\Fetcher\FetcherInterface;
use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;

/**
 * Class NodeInterfaceType
 */
class NodeInterfaceType extends AbstractInterfaceType
{
    /** @var  FetcherInterface */
    protected $fetcher;

    /**
     * @return string
     */
    public function getName()
    {
        return 'NodeInterface';
    }

    /**
     * @param \Youshido\GraphQL\Config\Object\InterfaceTypeConfig $config
     */
    public function build($config)
    {
        $config->addField(new GlobalIdField('NodeInterface'));
    }

    /**
     * @param mixed $object
     *
     * @return mixed|null
     */
    public function resolveType($object)
    {
        if ($this->fetcher) {
            return $this->fetcher->resolveType($object);
        }

        return null;
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
