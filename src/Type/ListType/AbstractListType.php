<?php

namespace Youshido\GraphQL\Type\ListType;

use Youshido\GraphQL\Config\Object\ListTypeConfig;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\CompositeTypeInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeKind;

/**
 * Class AbstractListType
 */
abstract class AbstractListType extends AbstractObjectType implements CompositeTypeInterface
{
    /**
     * @var ListTypeConfig
     */
    protected $config;

    /**
     * AbstractListType constructor.
     */
    public function __construct()
    {
        $this->config = new ListTypeConfig(['itemType' => $this->getItemType()], $this);
    }

    /**
     * @return AbstractType
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
     * @param      $value
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
     * @inheritdoc
     */
    public function build($config)
    {
    }

    /**
     * @return bool
     */
    public function isCompositeType()
    {
        return true;
    }

    /**
     * @return AbstractType
     */
    public function getNamedType()
    {
        return $this->getItemType();
    }

    /**
     * @return string
     */
    final public function getKind()
    {
        return TypeKind::KIND_LIST;
    }

    /**
     * @return AbstractType
     */
    public function getTypeOf()
    {
        return $this->getNamedType();
    }

    /**
     * @param array $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        foreach ((array) $value as $keyValue => $valueItem) {
            $value[$keyValue] = $this->getItemType()->parseValue($valueItem);
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getValidationError($value = null)
    {
        if (!$this->isIterable($value)) {
            return 'The value is not an iterable.';
        }

        return $this->config->get('itemType')->getValidationError($this->validList($value, true));
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function isIterable($value)
    {
        return null === $value || is_array($value) || ($value instanceof \Traversable);
    }
}
