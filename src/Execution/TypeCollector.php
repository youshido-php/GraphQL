<?php

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\AbstractUnionType;


/**
 * Class TypeCollector
 */
class TypeCollector
{
    /** @var  TypeCollector */
    private static $instance;

    /** @var array */
    private $types = [];

    /** @var  bool */
    private $schemaCollected = false;

    /**
     * TypeCollector constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return TypeCollector
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new TypeCollector();
        }

        return self::$instance;
    }

    /**
     * @param AbstractSchema $schema
     */
    public function addSchema(AbstractSchema $schema)
    {
        if ($this->schemaCollected) {
            return;
        }

        $this->addType($schema->getQueryType());
        if ($schema->getMutationType()->hasFields()) {
            $this->addType($schema->getMutationType());
        }


        foreach ($schema->getTypes()->all() as $type) {
            $this->addType($type);
        }

        $this->schemaCollected = true;
    }

    /**
     * @param TypeInterface $type
     */
    public function addType(TypeInterface $type)
    {
        if (is_object($type) && array_key_exists($type->getName(), $this->types)) {
            return;
        }

        switch ($type->getKind()) {
            case TypeMap::KIND_INTERFACE:
            case TypeMap::KIND_UNION:
            case TypeMap::KIND_ENUM:
            case TypeMap::KIND_SCALAR:
                $this->insertType($type->getName(), $type);

                if ($type->getKind() === TypeMap::KIND_UNION) {
                    /** @var AbstractUnionType $type */
                    foreach ($type->getTypes() as $subType) {
                        $this->addType($subType);
                    }
                }

                break;

            case TypeMap::KIND_INPUT_OBJECT:
            case TypeMap::KIND_OBJECT:
                /** @var AbstractObjectType $namedType */
                $namedType = $type->getNamedType();
                $this->checkAndInsertInterfaces($namedType);

                if ($this->insertType($namedType->getName(), $namedType)) {
                    $this->collectFieldsArgsTypes($namedType);
                }

                break;

            case TypeMap::KIND_LIST:
                $this->addType($type->getNamedType());
                break;

            case TypeMap::KIND_NON_NULL:
                $this->addType($type->getNamedType());

                break;
        }
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return array_values($this->types);
    }

    /**
     * Clear collected types
     */
    public function clear()
    {
        $this->types           = [];
        $this->schemaCollected = false;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getInterfacePossibleTypes($name)
    {
        $possibleTypes = [];
        foreach ($this->types as $type) {
            /** @var $type AbstractObjectType */
            if ($type->getKind() === TypeMap::KIND_OBJECT) {
                if ($interfaces = $type->getConfig()->getInterfaces()) {
                    foreach ($interfaces as $interface) {
                        if ($interface->getName() === $name) {
                            $possibleTypes[] = $type;
                        }
                    }
                }
            }
        }

        return $possibleTypes;
    }

    /**
     * @param $type AbstractObjectType
     */
    private function checkAndInsertInterfaces($type)
    {
        foreach ((array) $type->getConfig()->getInterfaces() as $interface) {
            /** @var AbstractInterfaceType $interface */
            $this->insertType($interface->getName(), $interface);
        }
    }

    /**
     * @param $type AbstractObjectType
     */
    private function collectFieldsArgsTypes($type)
    {
        foreach ($type->getConfig()->getFields() as $field) {
            $arguments = $field->getConfig()->getArguments();

            if (is_array($arguments)) {
                foreach ($arguments as $argument) {
                    $this->addType($argument->getType());
                }
            }

            $this->addType($field->getType());
        }
    }

    /**
     * @param string        $name
     * @param TypeInterface $type
     *
     * @return bool
     */
    private function insertType($name, $type)
    {
        if (!isset($this->types[$name])) {
            $this->types[$name] = $type;

            return true;
        }

        return false;
    }
}
