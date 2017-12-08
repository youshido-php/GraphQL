<?php

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

/**
 * Class TypeFactory
 */
class TypeFactory
{
    private static $objectsHash = [];

    /**
     * @param string|AbstractScalarType $type
     *
     * @throws ConfigurationException
     * @return AbstractScalarType
     */
    public static function getScalarType($type)
    {
        if (TypeService::isScalarType($type)) {
            if (is_object($type)) {
                return $type;
            }
            if (empty(self::$objectsHash[$type])) {
                $name = ucfirst($type);

                $name = $name === 'Datetime' ? 'DateTime' : $name;
                $name = $name === 'Datetimetz' ? 'DateTimeTz' : $name;

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
            TypeMap::TYPE_DATETIMETZ,
        ];
    }
}
