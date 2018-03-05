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
 * created: 5/11/16 9:19 PM
 */

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

class TypeFactory
{
    private static $objectsHash = [];

    /**
     * @param string $type
     *
     * @throws ConfigurationException
     *
     * @return AbstractScalarType
     */
    public static function getScalarType($type)
    {
        if (TypeService::isScalarType($type)) {
            if (\is_object($type)) {
                return $type;
            }

            if (empty(self::$objectsHash[$type])) {
                $name = \ucfirst($type);

                $name = 'Datetime'   === $name ? 'DateTime' : $name;
                $name = 'Datetimetz' === $name ? 'DateTimeTz' : $name;

                $className                = 'Youshido\GraphQL\Type\Scalar\\' . $name . 'Type';
                self::$objectsHash[$type] = new $className();
            }

            return self::$objectsHash[$type];
        }

        throw new ConfigurationException('Configuration problem with type ' . $type);
    }

    /**
     * @return string[]
     */
    public static function getScalarTypesNames()
    {
        return [
            TypeMap::TYPE_INT,
            TypeMap::TYPE_FLOAT,
            TypeMap::TYPE_STRING,
            TypeMap::TYPE_BOOLEAN,
            TypeMap::TYPE_ID,
            TypeMap::TYPE_DATETIME,
            TypeMap::TYPE_DATE,
            TypeMap::TYPE_TIMESTAMP,
            TypeMap::TYPE_DATETIMETZ,
        ];
    }
}
