<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:40 PM
*/

namespace Youshido\GraphQL;


use Youshido\GraphQL\Type\Config\ObjectTypeConfig;
use Youshido\GraphQL\Type\Config\SchemaConfig;
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

        $this->config = new SchemaConfig($config, $this);

        $this->buildTypes($this->config->getQuery()->getConfig());
    }

    public function getRootQueryTypeName()
    {
        return 'RootQueryType';
    }

    public function buildTypes(ObjectTypeConfig $config)
    {
    }

    public function addQuery(ObjectType $query, $name = '')
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