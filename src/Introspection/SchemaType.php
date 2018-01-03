<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Field\TypesField;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class SchemaType
 */
class SchemaType extends AbstractObjectType
{
    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Schema';
    }

    /**
     * @param AbstractSchema $value
     *
     * @return AbstractObjectType
     */
    public function resolveQueryType($value)
    {
        return $value->getQueryType();
    }

    /**
     * @param AbstractSchema $value
     *
     * @return null|\Youshido\GraphQL\Type\Object\AbstractObjectType
     */
    public function resolveMutationType($value)
    {
        return $value->getMutationType();
    }

    /**
     * @return null
     */
    public function resolveSubscriptionType()
    {
        return null;
    }

    /**
     * @param AbstractSchema|Field $value
     *
     * @return \Youshido\GraphQL\Directive\DirectiveInterface[]
     */
    public function resolveDirectives($value)
    {
        return $value->getDirectives();
    }

    /**
     * @param \Youshido\GraphQL\Config\Object\ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField(new Field([
                'name'    => 'queryType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveQueryType'],
            ]))
            ->addField(new Field([
                'name'    => 'mutationType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveMutationType'],
            ]))
            ->addField(new Field([
                'name'    => 'subscriptionType',
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveSubscriptionType'],
            ]))
            ->addField(new TypesField())
            ->addField(new Field([
                'name'    => 'directives',
                'type'    => new ListType(new DirectiveType()),
                'resolve' => [$this, 'resolveDirectives'],
            ]));
    }
}
