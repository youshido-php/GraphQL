<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/30/16 1:32 PM
*/

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractInterfaceType;
use Youshido\GraphQL\Type\Scalar\StringType;

class SummaryInterface extends AbstractInterfaceType
{

    public function build(TypeConfigInterface $config)
    {
        $config->addField('summary', new NonNullType(new StringType()));
    }

    public function getName()
    {
        return "SummaryInterface";
    }

}
