<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 10:25 PM
*/

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Type\Traits\FinalTypesConfigTrait;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

final class InputObjectType extends AbstractInputObjectType
{

    use FinalTypesConfigTrait;

    protected function getOutputType()
    {
        throw new ConfigurationException('You must define output type');
    }
}