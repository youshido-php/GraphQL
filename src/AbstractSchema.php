<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/5/16 9:24 PM
*/

namespace Youshido\GraphQL;


use Youshido\GraphQL\Type\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;

abstract class AbstractSchema
{

    /** @var SchemaConfig */
    protected $config;

    public function __construct($config = [])
    {
        if (!array_key_exists('query', $config)) {
            $config['query'] = new ObjectType(['name' => $this->getName() . 'Query']);
        }
        if (!array_key_exists('mutation', $config)) {
            $config['mutation'] = new ObjectType(['name' => $this->getName() . 'Mutation']);
        }

        $this->config = new SchemaConfig($config, $this);

        $this->build($this->config);
    }

    abstract public function build(SchemaConfig $config);

    public function addQuery($name, AbstractObjectType $query, $config = [])
    {
        $this->getQueryType()->getConfig()->addField($name, $query, $config);
    }

    public function addMutation($name, AbstractObjectType $query, $config = [])
    {
        $this->getMutationType()->getConfig()->addField($name, $query, $config);
    }

    final public function getQueryType()
    {
        return $this->config->getQuery();
    }

    final public function getMutationType()
    {
        return $this->config->getMutation();
    }

    public function getName()
    {
        $defaultName = 'RootSchema';

        return $this->config ? $this->config->get('name', $defaultName) : $defaultName;
    }
}
