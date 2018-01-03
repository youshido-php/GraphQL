<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class ListTypeConfig
 *
 * @method TypeInterface getItemType()
 * @method void setItemType(TypeInterface $itemType)
 */
class ListTypeConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'itemType' => ['type' => PropertyType::TYPE_GRAPHQL_TYPE, 'required' => true],
        ];
    }
}
