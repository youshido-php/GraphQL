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
 * Date: 03.12.15.
 */

namespace Youshido\GraphQL\Type\ListType;

use Youshido\GraphQL\Config\Object\ListTypeConfig;
use Youshido\GraphQL\Type\CompositeTypeInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractListType extends AbstractObjectType implements CompositeTypeInterface
{
    /**
     * @var ListTypeConfig
     */
    protected $config;

    public function __construct()
    {
        $this->config = new ListTypeConfig(['itemType' => $this->getItemType()], $this);
    }

    /**
     * @return AbstractObjectType
     */
    abstract public function getItemType();

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        if (!$this->isIterable($value)) {
            return false;
        }

        return $this->validList($value);
    }

    /**
     * {@inheritdoc}
     */
    public function build($config): void
    {
    }

    public function isCompositeType()
    {
        return true;
    }

    public function getNamedType()
    {
        return $this->getItemType();
    }

    final public function getKind()
    {
        return TypeMap::KIND_LIST;
    }

    public function getTypeOf()
    {
        return $this->getNamedType();
    }

    public function parseValue($value)
    {
        foreach ((array) $value as $keyValue => $valueItem) {
            $value[$keyValue] = $this->getItemType()->parseValue($valueItem);
        }

        return $value;
    }

    public function getValidationError($value = null)
    {
        if (!$this->isIterable($value)) {
            return 'The value is not an iterable.';
        }

        return $this->config->get('itemType')->getValidationError($this->validList($value, true));
    }

    /**
     * @param $value
     * @param bool $returnValue
     *
     * @return bool
     */
    protected function validList($value, $returnValue = false)
    {
        $itemType = $this->config->get('itemType');

        if ($value && $itemType->isInputType()) {
            foreach ($value as $item) {
                if (!$itemType->isValidValue($item)) {
                    return $returnValue ? $item : false;
                }
            }
        }

        return true;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function isIterable($value)
    {
        return null === $value || \is_array($value) || ($value instanceof \Traversable);
    }
}
