<?php

namespace Youshido\GraphQL\Type\ListType;

use Youshido\GraphQL\Config\Object\ListTypeConfig;
use Youshido\GraphQL\Type\AbstractType;

/**
 * Class ListType
 */
final class ListType extends AbstractListType
{
    /**
     * ListType constructor.
     *
     * @param AbstractType $itemType
     */
    public function __construct($itemType)
    {
        $this->config = new ListTypeConfig(['itemType' => $itemType], $this, true);
    }

    /**
     * @return AbstractType
     */
    public function getItemType()
    {
        return $this->getConfig()->get('itemType');
    }

    /**
     * @return null
     */
    public function getName()
    {
        return null;
    }
}
