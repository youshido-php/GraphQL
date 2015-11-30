<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 12:51 AM
*/

namespace Youshido\GraphQL\Type;


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
}