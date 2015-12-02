<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:40 PM
*/

namespace Youshido\GraphQL;

use Youshido\GraphQL\Type\Config\SchemaConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\InputObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;

class Schema
{

    /** @var SchemaConfig */
    protected $config;

    public function __construct($config = [])
    {
        if (!array_key_exists('query', $config)) {
            $config['query'] = new ObjectType(['name' => $this->getRootQueryTypeName()]);
        }

        if (!array_key_exists('mutation', $config)) {
            $config['mutation'] = new InputObjectType(['name' => $this->getRootQueryTypeName()]);
        }

        $this->config = new SchemaConfig($config, $this);

        $this->buildTypes($this->config->getQuery()->getConfig());
        $this->buildMutations($this->config->getMutation()->getConfig());
    }

    public function getRootQueryTypeName()
    {
        return 'RootQueryType';
    }

    public function buildTypes(TypeConfigInterface $config)
    {
    }

    public function buildMutations(TypeConfigInterface $config)
    {
    }

    public function addQuery(ObjectType $query)
    {
        if (empty($name)) {
            $name = $query->getName();
        }

        $this->getQueryType()->getConfig()->addField($name, $query);
    }

    public function getQueryType()
    {
        return $this->config->getQuery();
    }

    public function getMutationType()
    {
        return $this->config->getMutation();
    }

}