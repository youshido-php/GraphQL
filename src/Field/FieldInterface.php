<?php
/**
 * Date: 17.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;


interface FieldInterface
{
    public function getType();

    public function getName();
}