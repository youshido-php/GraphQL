<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class InputValueType extends AbstractObjectType
{

    public static function resolveType($value)
    {
        /** @var AbstractSchema|Field $value */
        return $value->getConfig()->getType();
    }

    public function build($config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField(new Field([
                'name'    => 'type',
                'type'    => new QueryType(),
                'resolve' => [get_class($this), 'resolveType']
            ]))
            ->addField('defaultValue', TypeMap::TYPE_STRING);
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__InputValue';
    }
}
