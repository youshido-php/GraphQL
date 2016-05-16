<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Field\SchemaField;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\CompositeTypeInterface;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

class QueryType extends AbstractObjectType
{

    use TypeCollectorTrait;

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
            ->addField('ofType', [
                'type'    => new QueryType(),
                'resolve' => function ($value) {
                    if ($value instanceof CompositeTypeInterface) {
                        return $value->getTypeOf();
                    }

                    return null;
                }
            ])
            ->addField(new Field([
                'name'    => 'inputFields',
                'type'    => new ListType(new InputValueType()),
                'resolve' => function ($value) {
                    /** @var $value AbstractObjectType */
                    if ($value instanceof AbstractScalarType) {
                        return null;
                    }

                    if ($value->getKind() == TypeMap::KIND_INPUT_OBJECT) {
                        return $value->getConfig()->getFields() ?: null;
                    } else {
                        return $value->getConfig()->getArguments() ?: null;
                    }
                }
            ]))
            ->addField(new Field([
                'name'    => 'enumValues',
                'type'    => new EnumValueType(),
                'resolve' => function ($value) {
                    /** @var $value AbstractType|AbstractEnumType */
                    if ($value && $value->getKind() == TypeMap::KIND_ENUM) {
                        $data = [];
                        foreach ($value->getValues() as $enumValue) {
                            if (!array_key_exists('description', $enumValue)) {
                                $value['description'] = '';
                            }
                            if (!array_key_exists('isDeprecated', $enumValue)) {
                                $value['isDeprecated'] = false;
                            }
                            if (!array_key_exists('deprecationReason', $enumValue)) {
                                $value['deprecationReason'] = '';
                            }

                            $data[] = $value;
                        }

                        return $data;
                    }

                    return null;
                }
            ]))
            ->addField(new Field([
                'name'    => 'fields',
                'type'    => new ListType(new FieldType()),
                'resolve' => function ($value) {
                    /** @var AbstractType $value */
                    if (!$value || in_array($value->getKind(), [
                            TypeMap::KIND_SCALAR,
                            TypeMap::KIND_UNION,
                            TypeMap::KIND_INPUT_OBJECT
                        ])
                    ) {
                        return null;
                    }

                    /** @var AbstractObjectType $value */
                    $fields = $value->getConfig()->getFields();

                    foreach ($fields as $key => $field) {
                        if (in_array($field->getName(), ['__type', '__schema'])) {
                            unset($fields[$key]);
                        }
                    }

                    return $fields;
                }
            ]))
            ->addField(new Field([
                'name'    => 'interfaces',
                'type'    => new ListType(new QueryType()),
                'resolve' => function ($value) {
                    /** @var $value AbstractType */
                    if ($value->getKind() == TypeMap::KIND_OBJECT) {
                        /** @var $value AbstractObjectType */
                        return $value->getConfig()->getInterfaces() ?: [];
                    } elseif ($value->getKind() == TypeMap::KIND_UNION) {
                        return null;
                    }

                    return [];
                }
            ]))
            ->addField('possibleTypes', [
                'type'    => new ListType(new QueryType()),
                'resolve' => function ($value) {
                    if (!$value) {
                        return null;
                    }

                    /** @var $value AbstractObjectType */
                    if ($value->getKind() == TypeMap::KIND_INTERFACE) {
                        $this->collectTypes(SchemaField::$schema->getQueryType());

                        $possibleTypes = [];
                        foreach ($this->types as $type) {
                            /** @var $type AbstractObjectType */
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
                        /** @var $value AbstractUnionType */
                        return $value->getTypes();
                    }

                    return null;
                }
            ]);
    }
}
