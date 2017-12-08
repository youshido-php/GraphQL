<?php

namespace Youshido\GraphQL\Relay\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Relay\Fetcher\FetcherInterface;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Relay\NodeInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

/**
 * Class NodeField
 */
class NodeField extends AbstractField
{
    /** @var  FetcherInterface */
    protected $fetcher;

    /** @var NodeInterfaceType */
    protected $type;

    /**
     * NodeField constructor.
     *
     * @param FetcherInterface $fetcher
     */
    public function __construct(FetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;
        $this->type    = (new NodeInterfaceType())->setFetcher($this->fetcher);

        parent::__construct([]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Fetches an object given its ID';
    }

    /**
     * @param FieldConfig $config
     */
    public function build(FieldConfig $config)
    {
        $config->addArgument(new InputField([
            'name'        => 'id',
            'type'        => new NonNullType(new IdType()),
            'description' => 'The ID of an object',
        ]));
    }

    /**
     * @return NodeInterfaceType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function resolve($value, array $args, ResolveInfoInterface $info)
    {
        list($type, $id) = Node::fromGlobalId($args['id']);

        return $this->fetcher->resolveNode($type, $id);
    }
}
