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
 * created: 5/5/16 9:24 PM
 */

namespace Youshido\GraphQL\Schema;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Type\SchemaDirectivesList;
use Youshido\GraphQL\Type\SchemaTypesList;

abstract class AbstractSchema
{
    /** @var SchemaConfig */
    protected $config;

    public function __construct($config = [])
    {
        if (!\array_key_exists('query', $config)) {
            $config['query'] = new InternalSchemaQueryObject(['name' => $this->getName($config) . 'Query']);
        }

        if (!\array_key_exists('mutation', $config)) {
            $config['mutation'] = new InternalSchemaMutationObject(['name' => $this->getName($config) . 'Mutation']);
        }

        if (!\array_key_exists('types', $config)) {
            $config['types'] = [];
        }

        $this->config = new SchemaConfig($config, $this);

        $this->build($this->config);
    }

    abstract public function build(SchemaConfig $config);

    public function addQueryField($field, $fieldInfo = null): void
    {
        $this->getQueryType()->addField($field, $fieldInfo);
    }

    public function addMutationField($field, $fieldInfo = null): void
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
     * @return SchemaTypesList
     */
    public function getTypesList()
    {
        return $this->config->getTypesList();
    }

    /**
     * @return SchemaDirectivesList
     */
    public function getDirectiveList()
    {
        return $this->config->getDirectiveList();
    }

    public function getName($config)
    {
        $defaultName = 'RootSchema';

        return $config['name'] ?? $defaultName;
    }
}
