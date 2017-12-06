<?php

namespace Youshido\GraphQL\Config\Schema;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\SchemaDirectiveCollection;
use Youshido\GraphQL\Type\SchemaTypesCollection;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class SchemaConfig
 */
class SchemaConfig extends AbstractConfig
{
    /**
     * @var SchemaTypesCollection
     */
    private $types;

    /**
     * @var SchemaDirectiveCollection;
     */
    private $directives;

    public function __construct(array $configData, $contextObject = null, $finalClass = false)
    {
        $this->types      = new SchemaTypesCollection();
        $this->directives = new SchemaDirectiveCollection();

        parent::__construct($configData, $contextObject, $finalClass);
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'query'      => ['type' => TypeService::TYPE_OBJECT_TYPE, 'required' => true],
            'mutation'   => ['type' => TypeService::TYPE_OBJECT_TYPE],
            'types'      => ['type' => TypeService::TYPE_ARRAY],
            'directives' => ['type' => TypeService::TYPE_ARRAY],
            'name'       => ['type' => TypeService::TYPE_STRING],
        ];
    }

    /**
     * @return AbstractObjectType
     */
    public function getQuery()
    {
        return $this->data['query'];
    }

    /**
     * @param $query AbstractObjectType
     *
     * @return SchemaConfig
     */
    public function setQuery($query)
    {
        $this->data['query'] = $query;

        return $this;
    }

    /**
     * @return ObjectType
     */
    public function getMutation()
    {
        return $this->get('mutation');
    }

    /**
     * @param $query AbstractObjectType
     *
     * @return SchemaConfig
     */
    public function setMutation($query)
    {
        $this->data['mutation'] = $query;

        return $this;
    }

    public function getName()
    {
        return $this->get('name', 'RootSchema');
    }

    /**
     * @return SchemaTypesCollection
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return SchemaDirectiveCollection
     */
    public function getDirectives()
    {
        return $this->directives;
    }

    protected function build()
    {
        parent::build();
        if (!empty($this->data['types'])) {
            $this->types->addMany($this->data['types']);
        }
        if (!empty($this->data['directives'])) {
            $this->directives->addMany($this->data['directives']);
        }
    }
}
