<?php
/**
 * @author Paweł Dziok <pdziok@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


class Reference
{

    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}