<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\TypeInterface;

/**
 * Class Field
 */
final class Field extends AbstractField
{
    /**
     * @return TypeInterface
     */
    public function getType()
    {
        return $this->getConfig()->getType();
    }
}
