<?php

namespace Youshido\GraphQL\Schema;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Directive\IncludeDirective;
use Youshido\GraphQL\Directive\SkipDirective;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

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
        if (!array_key_exists('directives', $config)) {
            $config['directives'] = [SkipDirective::build(), IncludeDirective::build()];
        }

        $this->config = new SchemaConfig($config, $this, true);
        $this->build($this->config);

        $this->config->validate();
    }

    abstract public function build(SchemaConfig $config);

    /**
     * @return \Youshido\GraphQL\Type\Object\AbstractObjectType
     */
    final public function getQueryType()
    {
        return $this->config->getQuery();
    }

    /**
     * @return \Youshido\GraphQL\Type\Object\AbstractObjectType
     */
    final public function getMutationType()
    {
        return $this->config->getMutation();
    }

    /**
     * @param AbstractObjectType $type
     */
    public function setQueryType(AbstractObjectType $type)
    {
        $this->config->setQuery($type);
    }

    /**
     * @param AbstractObjectType $type
     */
    public function setMutationType(AbstractObjectType $type)
    {
        $this->config->setMutation($type);
    }

    /**
     * @return AbstractType[]
     */
    public function getTypes()
    {
        return $this->config->getTypes();
    }

    /**
     * @return DirectiveInterface[]
     */
    public function getDirectives()
    {
        return $this->config->getDirectives();
    }

    /**
     * @param array $config
     *
     * @return string
     */
    public function getName($config)
    {
        $defaultName = 'RootSchema';

        return isset($config['name']) ? $config['name'] : $defaultName;
    }
}
