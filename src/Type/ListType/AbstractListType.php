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

    public function __construct($item = null)
    {
        if (!$item) {
            $item = $this->getItemType();
        }

        $this->config = new ListTypeConfig(['item' => $item], $this);
    }

    /**
     * @return AbstractObjectType
     */
    abstract public function getItemType();

    public function isValidValue($value)
    {
        return is_array($value);
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


    /**
     * @return String type name
     */
    public function getName()
    {
        return $this->getConfig()->get('item')->getName();
    }

    final public function getKind()
    {
        return TypeMap::KIND_LIST;
    }

    public function getTypeOf()
    {
        return $this->getConfig()->getItem();
    }


}
