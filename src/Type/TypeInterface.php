<?php

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Config\AbstractConfig;

/**
 * Interface TypeInterface
 */
interface TypeInterface
{
    /**
     * @return String type name
     */
    public function getName();

    /**
     * @return String predefined type kind
     */
    public function getKind();

    /**
     * @return String type description
     */
    public function getDescription();

    /**
     * Coercing value received as input to current type
     *
     * @param $value
     *
     * @return mixed
     */
    public function parseValue($value);

    /**
     * Coercing result to current type
     *
     * @param $value
     *
     * @return mixed
     */
    public function serialize($value);

    /**
     * @param $value mixed
     *
     * @return bool
     */
    public function isValidValue($value);

    /**
     * @return AbstractConfig
     */
    public function getConfig();

    /**
     * @return TypeInterface
     */
    public function getNullableType();

    /**
     * @return TypeInterface
     */
    public function getNamedType();
}
