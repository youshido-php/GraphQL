<?php

namespace Youshido\GraphQL\Schema;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Directive\IncludeDirective;
use Youshido\GraphQL\Directive\SkipDirective;
use Youshido\GraphQL\Type\SchemaDirectiveCollection;
use Youshido\GraphQL\Type\SchemaTypesCollection;

/**
 * Class AbstractSchema
 */
abstract class AbstractSchema
{
    /** @var SchemaConfig */
    protected $config;

    /**
     * AbstractSchema constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!array_key_exists('query', $config)) {
            $config['query'] = new InternalSchemaQueryObject(['name' => $this->getName($config) . 'Query']);
        }
        if (!array_key_exists('mutation', $config)) {
            $config['mutation'] = new InternalSchemaMutationObject(['name' => $this->getName($config) . 'Mutation']);
        }
        if (!array_key_exists('types', $config)) {
            $config['types'] = [];
        }

        if (!array_key_exists('directives', $config)) {
            $config['directives'] = [
                SkipDirective::build(),
                IncludeDirective::build(),
            ];
        }

        $this->config = new SchemaConfig($config, $this);

        $this->build($this->config);
    }

    abstract public function build(SchemaConfig $config);

    public function addQueryField($field, $fieldInfo = null)
    {
        $this->getQueryType()->addField($field, $fieldInfo);
    }

    public function addMutationField($field, $fieldInfo = null)
    {
        $this->getMutationType()->addField($field, $fieldInfo);
    }

    final public function getQueryType()
    {
        return $this->config->getQuery();
    }

    final public function getMutationType()
    {
        return $this->config->getMutation();
    }

    /**
     * @return SchemaTypesCollection
     */
    public function getTypes()
    {
        return $this->config->getTypes();
    }

    /**
     * @return SchemaDirectiveCollection
     */
    public function getDirectives()
    {
        return $this->config->getDirectives();
    }

    public function getName($config)
    {
        $defaultName = 'RootSchema';

        return isset($config['name']) ? $config['name'] : $defaultName;
    }
}
