<?php
/**
 * Date: 16.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection\Field;


use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Introspection\SchemaType;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class SchemaField extends AbstractField
{

    /** @var AbstractSchema */
    public static $schema;

    /**
     * @param AbstractSchema $schema
     */
    public function setSchema($schema)
    {
        self::$schema = $schema;
    }

    /**
     * @return AbstractSchema
     */
    public function getSchema()
    {
        return self::$schema;
    }

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return new SchemaType();
    }

    public function getName()
    {
        return '__schema';
    }

    public function resolve($value, $args = [], $type = null)
    {
        return $this->getSchema();
    }


}