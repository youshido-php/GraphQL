<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 27.11.15.
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class Field.
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
        return $this->_typeCache ? $this->_typeCache : ($this->_typeCache = $this->getConfigValue('type'));
    }

    public function getName()
    {
        return $this->_nameCache ? $this->_nameCache : ($this->_nameCache = $this->getConfigValue('name'));
    }
}
