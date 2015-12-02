<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:26 PM
*/

namespace Youshido\GraphQL\Type\Field;


use Youshido\GraphQL\Type\Config\InputFieldConfig;

class InputField extends Field
{
    /** @var InputFieldConfig */
    protected $config;

    public function __construct($config)
    {
        $this->config = new InputFieldConfig($config);
    }

}