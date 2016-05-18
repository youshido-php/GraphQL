<?php
/**
 * Date: 17.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;


use Youshido\GraphQL\Type\AbstractType;

interface FieldInterface
{
    /**
     * @return AbstractType
     */
    public function getType();

    public function getName();
}
