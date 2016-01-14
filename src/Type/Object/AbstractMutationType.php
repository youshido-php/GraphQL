<?php
/**
 * Date: 14.01.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;

abstract class AbstractMutationType extends AbstractObjectType
{

    public function build(TypeConfigInterface $config)
    {

    }

    abstract public function getOutputType();
}