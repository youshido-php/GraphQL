<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Execution\TypeCollector;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\CompositeTypeInterface;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

/**
 * Class QueryType
 */
class QueryType extends AbstractObjectType
{
    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Type';
    }

    /**
     * @param AbstractType $value
     *
     * @return null|AbstractType
     */
    public function resolveOfType(AbstractType $value)
    {
        if ($value instanceof CompositeTypeInterface) {
            return $value->getTypeOf();
        }

        return null;
    }

    /**
     * @param AbstractInputObjectType|AbstractObjectType $value
     *
     * @return null|Field[]
     */
    public function resolveInputFields($value)
    {
        if ($value instanceof AbstractInputObjectType) {
            return $value->getConfig()->getFields();
        }

        return null;
    }

    /**
     * @param AbstractType|AbstractEnumType $value
     * @param array                         $args
     *
     * @return array|null
     */
    public function resolveEnumValues($value, $args)
    {
        if ($value && $value->getKind() === TypeMap::KIND_ENUM) {
            $data = [];
            foreach ($value->getValues() as $enumValue) {
                if (!$args['includeDeprecated'] && (isset($enumValue['isDeprecated']) && $enumValue['isDeprecated'])) {
                    continue;
                }

                if (!array_key_exists('description', $enumValue)) {
                    $enumValue['description'] = '';
                }
                if (!array_key_exists('isDeprecated', $enumValue)) {
                    $enumValue['isDeprecated'] = false;
                }
                if (!array_key_exists('deprecationReason', $enumValue)) {
                    $enumValue['deprecationReason'] = null;
                }

                $data[] = $enumValue;
            }

            return $data;
        }

        return null;
    }

    /**
     * @param AbstractType $value
     * @param array        $args
     *
     * @return array|null
     */
    public function resolveFields($value, $args)
    {
        if (!$value || in_array($value->getKind(), [TypeMap::KIND_SCALAR, TypeMap::KIND_UNION, TypeMap::KIND_INPUT_OBJECT, TypeMap::KIND_ENUM], false)) {
            return null;
        }

        /** @var AbstractObjectType $value */
        return array_filter($value->getConfig()->getFields(), function ($field) use ($args) {
            /** @var $field Field */
            if ((!$args['includeDeprecated'] && $field->isDeprecated()) || in_array($field->getName(), ['__type', '__schema'], false)) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param AbstractType $value
     *
     * @return array|null
     */
    public function resolveInterfaces($value)
    {
        if ($value->getKind() === TypeMap::KIND_OBJECT) {
            /** @var $value AbstractObjectType */
            return $value->getConfig()->getInterfaces() ?: [];
        }

        return null;
    }

    /**
     * @param AbstractObjectType $value
     * @param array              $args
     * @param ResolveInfoInterface        $info
     *
     * @return array|null|AbstractObjectType[]|\Youshido\GraphQL\Type\Scalar\AbstractScalarType[]
     */
    public function resolvePossibleTypes($value, $args, ResolveInfoInterface $info)
    {
        if ($value->getKind() === TypeMap::KIND_INTERFACE) {
            $collector = TypeCollector::getInstance();
            $collector->addSchema($info->getExecutionContext()->getSchema());

            return $collector->getInterfacePossibleTypes($value->getName());
        }
        if ($value->getKind() === TypeMap::KIND_UNION) {
            /** @var $value AbstractUnionType */
            return $value->getTypes();
        }

        return null;
    }

    /**
     * @param \Youshido\GraphQL\Config\Object\ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('kind', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('ofType', [
                'type'    => new QueryType(),
                'resolve' => [$this, 'resolveOfType'],
            ])
            ->addField(new Field([
                'name'    => 'inputFields',
                'type'    => new ListType(new NonNullType(new InputValueType())),
                'resolve' => [$this, 'resolveInputFields'],
            ]))
            ->addField(new Field([
                'name'    => 'enumValues',
                'args'    => [
                    'includeDeprecated' => [
                        'type'         => new BooleanType(),
                        'defaultValue' => false,
                    ],
                ],
                'type'    => new ListType(new NonNullType(new EnumValueType())),
                'resolve' => [$this, 'resolveEnumValues'],
            ]))
            ->addField(new Field([
                'name'    => 'fields',
                'args'    => [
                    'includeDeprecated' => [
                        'type'         => new BooleanType(),
                        'defaultValue' => false,
                    ],
                ],
                'type'    => new ListType(new NonNullType(new FieldType())),
                'resolve' => [$this, 'resolveFields'],
            ]))
            ->addField(new Field([
                'name'    => 'interfaces',
                'type'    => new ListType(new NonNullType(new QueryType())),
                'resolve' => [$this, 'resolveInterfaces'],
            ]))
            ->addField('possibleTypes', [
                'type'    => new ListType(new NonNullType(new QueryType())),
                'resolve' => [$this, 'resolvePossibleTypes'],
            ]);
    }
}
