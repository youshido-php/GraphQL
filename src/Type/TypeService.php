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
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5/11/16 10:19 PM
 */

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Scalar\StringType;

class TypeService
{
    public const TYPE_CALLABLE = 'callable';

    public const TYPE_GRAPHQL_TYPE = 'graphql_type';

    public const TYPE_OBJECT_TYPE = 'object_type';

    public const TYPE_ARRAY_OF_OBJECT_TYPES = 'array_of_object_types';

    public const TYPE_OBJECT_INPUT_TYPE = 'object_input_type';

    public const TYPE_LIST = 'list';

    public const TYPE_BOOLEAN = TypeMap::TYPE_BOOLEAN;

    public const TYPE_STRING = TypeMap::TYPE_STRING;

    public const TYPE_ARRAY = 'array';

    public const TYPE_ARRAY_OF_FIELDS_CONFIG = 'array_of_fields';

    public const TYPE_ARRAY_OF_INPUT_FIELDS = 'array_of_inputs';

    public const TYPE_ENUM_VALUES = 'array_of_values';

    public const TYPE_ARRAY_OF_INTERFACES = 'array_of_interfaces';

    public const TYPE_ANY = 'any';

    public const TYPE_ANY_OBJECT = 'any_object';

    public const TYPE_ANY_INPUT = 'any_input';

    public static function resolveNamedType($object)
    {
        if (\is_object($object) && $object instanceof AbstractType) {
            return $object->getType();
        }

        if (null === $object) {
            return;
        }

        if (\is_scalar($object)) {
            return new StringType();
        }

        throw new \Exception('Invalid type');
    }

    /**
     * @param AbstractType|mixed $type
     *
     * @return bool
     */
    public static function isInterface($type)
    {
        if (!\is_object($type)) {
            return false;
        }

        return TypeMap::KIND_INTERFACE === $type->getKind();
    }

    /**
     * @param AbstractType|mixed $type
     *
     * @return bool
     */
    public static function isAbstractType($type)
    {
        if (!\is_object($type)) {
            return false;
        }

        return \in_array($type->getKind(), [TypeMap::KIND_INTERFACE, TypeMap::KIND_UNION], true);
    }

    public static function isScalarType($type)
    {
        if (\is_object($type)) {
            return $type instanceof AbstractScalarType || $type instanceof AbstractEnumType;
        }

        return \in_array(\mb_strtolower($type), TypeFactory::getScalarTypesNames(), true);
    }

    public static function isGraphQLType($type)
    {
        return $type instanceof AbstractType || self::isScalarType($type);
    }

    public static function isLeafType($type)
    {
        return $type instanceof AbstractEnumType || self::isScalarType($type);
    }

    public static function isObjectType($type)
    {
        return $type instanceof AbstractObjectType;
    }

    /**
     * @param AbstractType|mixed $type
     *
     * @return bool
     */
    public static function isInputType($type)
    {
        if (\is_object($type)) {
            $namedType = $type->getNullableType()->getNamedType();

            return ($namedType instanceof AbstractScalarType)
                   || ($type instanceof AbstractListType)
                   || ($namedType instanceof AbstractInputObjectType)
                   || ($namedType instanceof AbstractEnumType);
        }

        return self::isScalarType($type);
    }

    public static function isInputObjectType($type)
    {
        return $type instanceof AbstractInputObjectType;
    }

    public static function getPropertyValue($data, $path)
    {
        if (\is_object($data)) {
            $getter = $path;

            if ('is' !== \mb_substr($path, 0, 2)) {
                $getter = 'get' . self::classify($path);

                if (!\is_callable([$data, $getter])) {
                    $getter = 'is' . self::classify($path);
                }

                if (!\is_callable([$data, $getter])) {
                    $getter = self::classify($path);
                }
            }

            return \is_callable([$data, $getter]) ? $data->{$getter}() : ($data->{$path} ?? null);
        }

        return (\is_array($data) && \array_key_exists($path, $data)) ? $data[$path] : null;
    }

    protected static function classify($text)
    {
        $text       = \explode(' ', \str_replace(['_', '/', '-', '.'], ' ', $text));
        $textLength = \count($text);

        for ($i = 0; $i < $textLength; $i++) {
            $text[$i] = \ucfirst($text[$i]);
        }
        $text = \ucfirst(\implode('', $text));

        return $text;
    }
}
