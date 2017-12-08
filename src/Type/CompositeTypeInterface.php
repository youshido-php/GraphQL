<?php

namespace Youshido\GraphQL\Type;

/**
 * Interface CompositeTypeInterface
 */
interface CompositeTypeInterface
{
    /**
     * @return AbstractType
     */
    public function getTypeOf();
}
