<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:40 PM
*/

namespace Youshido\GraphQL;


use Youshido\GraphQL\Type\Config\SchemaConfig;

class Schema
{

    protected $config;

    public function __construct($config)
    {
        $this->config = new SchemaConfig($config, $this);
    }

}