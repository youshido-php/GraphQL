<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 9:19 PM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

class TypeFactory
{
    private static $objectsHash = [];

    /**
     * @param string $typeName
     *
     * @return AbstractScalarType
     */
    public static function getScalarType($typeName)
    {
        if (TypeMap::isScalarType($typeName)) {
            if (empty(self::$objectsHash[$typeName])) {
                $name = ucfirst($typeName);
                $name = $name == 'Datetime' ? 'DateTime' : $name;
                $name = $name == 'Datetimetz' ? 'DateTimeTz' : $name;

                $className                    = 'Youshido\GraphQL\Type\Scalar\\' . $name . 'Type';
                self::$objectsHash[$typeName] = new $className();
            }

            return self::$objectsHash[$typeName];
        } else {
            return null;
        }
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
