<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class Field
 *
 * @package Youshido\GraphQL\Type\Field
 *
 */
final class Field extends AbstractField
{
    protected $isFinal = true;

    protected $_typeCache;
    protected $_nameCache;

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return $this->_typeCache ?: ($this->_typeCache = $this->getConfigValue('type'));
    }

    public function getName()
    {
        return $this->_nameCache ?: ($this->_nameCache = $this->getConfigValue('name'));
    }

}
