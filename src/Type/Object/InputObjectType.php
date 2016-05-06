<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 10:25 PM
*/

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Type\Config\InputTypeConfigInterface;
use Youshido\GraphQL\Type\Traits\FinalTypesConfigTrait;

final class InputObjectType extends AbstractInputObjectType
{
    use FinalTypesConfigTrait;

    public function build(InputTypeConfigInterface $config)
    {

    }
}
