<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\ListType;


final class ListType extends AbstractListType
{

    public function getItemType()
    {
        return $this->config->getItem();
    }

    public function getName()
    {
        return null;
    }
}
