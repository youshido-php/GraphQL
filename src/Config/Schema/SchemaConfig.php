<?php

namespace Youshido\GraphQL\Config\Schema;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class SchemaConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method AbstractObjectType getQuery()
 * @method void setQuery(AbstractObjectType $query)
 *
 * @method AbstractObjectType getMutation()
 * @method void setMutation(AbstractObjectType $query)
 *
 * @method AbstractObjectType[] getTypes()
 * @method void setTypes(array $types)
 *
 * @method DirectiveInterface[] getDirectives()
 * @method void setDirectives(array $directives)
 */
class SchemaConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'query'      => ['type' => PropertyType::TYPE_OBJECT_TYPE, 'required' => true],
            'mutation'   => ['type' => PropertyType::TYPE_OBJECT_TYPE],
            'types'      => ['type' => PropertyType::TYPE_GRAPHQL_TYPE, 'default' => [], 'array' => true],
            'directives' => ['type' => PropertyType::TYPE_DIRECTIVE, 'default' => [], 'array' => true],
            'name'       => ['type' => PropertyType::TYPE_STRING, 'default' => 'Schema'],
        ];
    }

    /**
     * @param TypeInterface $type
     */
    public function addType(TypeInterface $type)
    {
        $this->data['types'][$type->getName()] = $type;
    }

    /**
     * @param string $name
     */
    public function removeType($name)
    {
        if (isset($this->data['types'][$name])) {
            unset($this->data['types'][$name]);
        }
    }

    /**
     * @param DirectiveInterface $directive
     */
    public function addDirective(DirectiveInterface $directive)
    {
        $this->data['directives'][$directive->getName()] = $directive;
    }

    /**
     * @param string $name
     */
    public function removeDirective($name)
    {
        if (isset($this->data['directives'][$name])) {
            unset($this->data['directives'][$name]);
        }
    }

    protected function build()
    {
        $directives = (array) (isset($this->data['directives']) ? $this->data['directives'] : []);
        $types      = (array) (isset($this->data['types']) ? $this->data['types'] : []);

        $this->data['directives'] = [];
        $this->data['types']      = [];

        foreach ($directives as $directive) {
            $this->addDirective($directive);
        }
        foreach ($types as $type) {
            $this->addType($type);
        }
    }
}
