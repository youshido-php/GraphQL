<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\ListType;


use Youshido\GraphQL\Type\Config\Object\ListTypeConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractListType extends AbstractObjectType
{

    public function __construct($item = null)
    {
        if (!$item) {
            $item = $this->getItem();
        }

        $this->config = new ListTypeConfig(['item' => $item], $this);
    }

    /**
     * @return AbstractObjectType
     */
    abstract public function getItem();

    public function isValidValue($value)
    {
        return is_array($value);
    }

    protected function build(TypeConfigInterface $config)
    {

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

}
