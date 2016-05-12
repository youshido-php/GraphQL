<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Enum;

final class EnumType extends AbstractEnumType
{

    public function getValues()
    {
        return $this->getConfig()->getValues();
    }

}
