<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:32 PM
*/

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;

class NodeInterface extends AbstractInterfaceType
{

    public function getName()
    {
        return 'NodeInterface';
    }

    public function build($config)
    {
        Node::addGlobalId($config);
        //$config->addField('id', new GlobalIdField());
    }


    public function resolveType($object)
    {
        /** Need to find the best way to pass "TypeResolver" here */
        return null;
    }

}
