<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
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

    public function isValidValue($value)
    {
        if (!is_null($value) && !is_array($value) && !($value instanceof \Traversable)) {
          $this->lastError = 'The value is not an iterable.';
          return false;
        }
        $this->lastError = null;

        $itemType = $this->config->get('itemType');

        if ($value && $itemType->isInputType()) {
            foreach ($value as $item) {
                if (!$itemType->isValidValue($item)) {
                  return false;
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

    public function getLastError()
    {
        return $this->lastError ?: $this->config->get('itemType')->getLastError();
    }

}
