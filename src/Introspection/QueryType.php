<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Type\CompositeTypeInterface;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

class QueryType extends AbstractObjectType
{
    use TypeCollectorTrait;

    public function resolve($value = null, $args = [], $type = null)
    {
        /** @var AbstractSchema|Field $value */
        if ($value instanceof AbstractSchema) {
            return $value->getQueryType();
        }

        return $value->getConfig()->getType();
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Type';
    }

    public function build($config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('kind', TypeMap::TYPE_STRING)
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('ofType', new QueryType(), [
                'resolve' => function ($value) {
                    if ($value instanceof CompositeTypeInterface) {
                        return $value->getTypeOf();
                    }

                    return null;
                }
            ])
            ->addField('inputFields', new InputValueListType())
            ->addField('enumValues', new EnumValueListType())
            ->addField('fields', new FieldListType())
            ->addField('interfaces', new InterfaceListType())
            ->addField('possibleTypes', new ListType(new QueryType()), [
                'resolve' => function ($value) {
                    if ($value) {
                        if ($value->getKind() == TypeMap::KIND_INTERFACE) {
                            $this->collectTypes(SchemaType::$schema->getQueryType());

                            $possibleTypes = [];
                            foreach ($this->types as $type) {
                                /** @var $type TypeInterface */
                                if ($type->getKind() == TypeMap::KIND_OBJECT) {
                                    $interfaces = $type->getConfig()->getInterfaces();

                                    if ($interfaces) {
                                        foreach ($interfaces as $interface) {
                                            if (get_class($interface) == get_class($value)) {
                                                $possibleTypes[] = $type;
                                            }
                                        }
                                    }
                                }
                            }

                            return $possibleTypes ?: [];
                        } elseif ($value->getKind() == TypeMap::KIND_UNION) {
                            return $value->getTypes();
                        }

                    }

                    return null;
                }
            ]);
    }
}
