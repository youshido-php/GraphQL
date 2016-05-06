<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\ListType;


use Youshido\GraphQL\Type\Traits\FinalTypesConfigTrait;

final class ListType extends AbstractListType
{

    use FinalTypesConfigTrait {
        getName as getConfigName;
    }

    public function getItemType()
    {
        return $this->config->getItem();
    }

    public function getName()
    {
        return null;
    }
}
