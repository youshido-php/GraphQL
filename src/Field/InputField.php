<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class InputField
 */
final class InputField extends AbstractInputField
{
    protected $isFinal = true;

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return $this->getConfigValue('type');
    }
}
