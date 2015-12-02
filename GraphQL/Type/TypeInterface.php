<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 12:51 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Field\Field;

Interface TypeInterface
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
     * @return mixed
     */
    public function parseValue($value);

    /**
     * Coercing result to current type
     *
     * @param $value
     * @return mixed
     */
    public function serialize($value);

    /**
     * @return null|Field[]
     */
    public function getFields();

    /**
     * @return null|Field[]
     */
    public function getArguments();

    /**
     * @param $name string
     *
     * @return bool
     */
    public function hasField($name);

    /**
     * @param $name string
     *
     * @return bool
     */
    public function hasArgument($name);

    /**
     * @param $name string
     *
     * @return Field|null
     */
    public function getField($name);

    /**
     * @param $name string
     *
     * @return Field|null
     */
    public function getArgument($name);

    /**
     * @param $value mixed
     *
     * @return bool
     */
    public function isValidValue($value);
}