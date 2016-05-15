<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\FieldFactory;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class SchemaType extends AbstractObjectType
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

    public function resolve($value = null, $args = [], $type = null)
    {
        return $this->getSchema();
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Schema';
    }

    public function build($config)
    {
        $config
            ->addField(FieldFactory::fromTypeWithResolver('queryType', new QueryType()))
            ->addField(FieldFactory::fromTypeWithResolver('mutationType', new MutationType()))
            ->addField('subscriptionType', new ObjectType([
                'name'    => '__Subscription',
                'fields'  => ['name' => ['type' => TypeMap::TYPE_STRING]],
                'resolve' => function () {
                    return null;
                }
            ]))
            ->addField(FieldFactory::fromTypeWithResolver('types', new QueryListType()))
            ->addField(FieldFactory::fromTypeWithResolver('directives', new DirectiveListType()));
    }
}
