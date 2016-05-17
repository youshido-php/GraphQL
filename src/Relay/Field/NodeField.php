<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:46 PM
*/

namespace Youshido\GraphQL\Relay\Field;


use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Relay\Fetcher\FetcherInterface;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Relay\NodeInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class NodeField extends AbstractField
{

    /** @var  FetcherInterface */
    protected $fetcher;

    public function __construct(FetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;

        parent::__construct([]);
    }

    public function getName()
    {
        return 'node';
    }

    public function getDescription()
    {
        return 'Fetches an object given its ID';
    }

    public function build(FieldConfig $config)
    {
        $config->addArgument(new InputField([
            'name'        => 'id',
            'type'        => new NonNullType(new IdType()),
            'description' => 'The ID of an object'
        ]));
    }

    public function getType()
    {
        return (new NodeInterfaceType())->setFetcher($this->fetcher);
    }

    public function resolve($value, $args = [], $type = null)
    {
        list($type, $id) = Node::fromGlobalId($args['id']);

        return $this->fetcher->resolveNode($type, $id);
    }


}
