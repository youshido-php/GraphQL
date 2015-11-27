<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:24 AM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\ObjectTypeConfig;

class ObjectType extends AbstractType
{

    protected $name = "Object";

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = new ObjectTypeConfig($config);

    }

    public function parseValue($value)
    {
        return $value;
    }

    public function serialize($value)
    {
        return $value;
    }


}