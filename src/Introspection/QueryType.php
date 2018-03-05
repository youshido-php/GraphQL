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
/**
 * Date: 03.12.15.
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
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

class QueryType extends AbstractObjectType
{
    use TypeCollectorTrait;

    /**
     * @return string type name
     */
    public function getName()
    {
        return '__Type';
    }

    public function resolveOfType(AbstractType $value)
    {
        if ($value instanceof CompositeTypeInterface) {
            return $value->getTypeOf();
        }
    }

    public function resolveInputFields($value)
    {
        if ($value instanceof AbstractInputObjectType) {
            /* @var AbstractObjectType $value */
            return $value->getConfig()->getFields();
        }
    }

    public function resolveEnumValues($value, $args)
    {
        /** @var $value AbstractType|AbstractEnumType */
        if ($value && TypeMap::KIND_ENUM === $value->getKind()) {
            $data = [];

            foreach ($value->getValues() as $enumValue) {
                if (!$args['includeDeprecated'] && (isset($enumValue['isDeprecated']) && $enumValue['isDeprecated'])) {
                    continue;
                }

                if (!\array_key_exists('description', $enumValue)) {
                    $enumValue['description'] = '';
                }

                if (!\array_key_exists('isDeprecated', $enumValue)) {
                    $enumValue['isDeprecated'] = false;
                }

                if (!\array_key_exists('deprecationReason', $enumValue)) {
                    $enumValue['deprecationReason'] = null;
                }

                $data[] = $enumValue;
            }

            return $data;
        }
    }

    public function resolveFields($value, $args)
    {
        /** @var AbstractType $value */
        if (!$value || \in_array($value->getKind(), [TypeMap::KIND_SCALAR, TypeMap::KIND_UNION, TypeMap::KIND_INPUT_OBJECT, TypeMap::KIND_ENUM], true)
        ) {
            return;
        }

        /* @var AbstractObjectType $value */
        return \array_filter($value->getConfig()->getFields(), static function ($field) use ($args) {
            /** @var $field Field */
            if (\in_array($field->getName(), ['__type', '__schema'], true) || (!$args['includeDeprecated'] && $field->isDeprecated())) {
                return false;
            }

            return true;
        });
    }

    public function resolveInterfaces($value)
    {
        /** @var $value AbstractType */
        if (TypeMap::KIND_OBJECT === $value->getKind()) {
            /* @var $value AbstractObjectType */
            return $value->getConfig()->getInterfaces() ?: [];
        }
    }

    public function resolvePossibleTypes($value, $args, ResolveInfo $info)
    {
        /** @var $value AbstractObjectType */
        if (TypeMap::KIND_INTERFACE === $value->getKind()) {
            $schema = $info->getExecutionContext()->getSchema();
            $this->collectTypes($schema->getQueryType());

            foreach ($schema->getTypesList()->getTypes() as $type) {
                $this->collectTypes($type);
            }

            $possibleTypes = [];

            foreach ($this->types as $type) {
                /** @var $type AbstractObjectType */
                if (TypeMap::KIND_OBJECT === $type->getKind()) {
                    $interfaces = $type->getConfig()->getInterfaces();

                    if ($interfaces) {
                        foreach ($interfaces as $interface) {
                            if ($interface->getName() === $value->getName()) {
                                $possibleTypes[] = $type;
                            }
                        }
                    }
                }
            }

            return $possibleTypes;
        }

        if (TypeMap::KIND_UNION === $value->getKind()) {
            /* @var $value AbstractUnionType */
            return $value->getTypes();
        }
    }

    public function build($config): void
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('kind', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('ofType', [
                'type'    => new self(),
                'resolve' => [$this, 'resolveOfType'],
            ])
            ->addField(new Field([
                'name'    => 'inputFields',
                'type'    => new ListType(new NonNullType(new InputValueType())),
                'resolve' => [$this, 'resolveInputFields'],
            ]))
            ->addField(new Field([
                'name' => 'enumValues',
                'args' => [
                    'includeDeprecated' => [
                        'type'         => new BooleanType(),
                        'defaultValue' => false,
                    ],
                ],
                'type'    => new ListType(new NonNullType(new EnumValueType())),
                'resolve' => [$this, 'resolveEnumValues'],
            ]))
            ->addField(new Field([
                'name' => 'fields',
                'args' => [
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
                'type'    => new ListType(new NonNullType(new self())),
                'resolve' => [$this, 'resolveInterfaces'],
            ]))
            ->addField('possibleTypes', [
                'type'    => new ListType(new NonNullType(new self())),
                'resolve' => [$this, 'resolvePossibleTypes'],
            ]);
    }
}
