<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Field\TypesField;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class SchemaType extends AbstractObjectType
{

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Schema';
    }

    public static function resolveQueryType($value)
    {
        /** @var AbstractSchema|Field $value */
        return $value->getQueryType();
    }

    public static function resolveMutationType($value)
    {
        /** @var AbstractSchema|Field $value */
        return $value->getMutationType()->hasFields() ? $value->getMutationType() : null;
    }

    public static function resolveSubscriptionType() {
        return null;
    }

    public function build($config)
    {
        $config
            ->addField(new Field([
                'name'    => 'queryType',
                'type'    => new QueryType(),
                'resolve' => [get_class($this), 'resolveQueryType']
            ]))
            ->addField(new Field([
                'name'    => 'mutationType',
                'type'    => new QueryType(),
                'resolve' => [get_class($this), 'resolveMutationType']
            ]))
            ->addField(new Field([
                'name'    => 'subscriptionType',
                'type'    => new ObjectType([
                    'name'   => '__Subscription',
                    'fields' => [
                        'name' => ['type' => TypeMap::TYPE_STRING]
                    ]
                ]),
                'resolve' => [get_class($this), 'resolveSubscriptionType']
            ]))
            ->addField(new TypesField())
            ->addField(new Field([
                'name' => 'directives',
                'type' => new ListType(new DirectiveType())
            ]));
    }
}
