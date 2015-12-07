<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:40 PM
*/

namespace Youshido\GraphQL;

use Youshido\GraphQL\Type\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\InputObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;

class Schema
{

    /** @var SchemaConfig */
    protected $config;

    public function __construct($config = [])
    {
        if (!array_key_exists('query', $config)) {
            $config['query'] = new ObjectType(['name' => $this->getName()]);
        }

        if (!array_key_exists('mutation', $config)) {
            $config['mutation'] = new InputObjectType(['name' => $this->getName()]);
        }

        $this->config = new SchemaConfig($config, $this);

        $this->build($this->config);
    }

    public function build(SchemaConfig $config)
    {
    }

    public function addQuery($name, AbstractObjectType $query, $config = [])
    {
        $this->getQueryType()->getConfig()->addField($name, $query, $config);
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