<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 10:25 PM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

final class InputObjectType extends AbstractInputObjectType
{
    protected function buildFields(TypeConfigInterface $config) {}
    protected function buildArguments(TypeConfigInterface $config) {}


}