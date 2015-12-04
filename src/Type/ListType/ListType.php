<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\ListType;


use Youshido\GraphQL\Type\Traits\FinalTypesConfigTrait;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

final class ListType extends AbstractListType
{

    use FinalTypesConfigTrait;

    public function getItem()
    {
        throw new ConfigurationException('You must define type of List Item');
    }

}