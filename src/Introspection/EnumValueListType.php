<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\TypeMap;

class EnumValueListType extends AbstractListType
{

    public function getItem()
    {
        return new EnumValueType();
    }

    public function build(TypeConfigInterface $config)
    {
        $config->addArgument('includeDeprecated', 'boolean', ['default' => false]);
    }

    public function resolve($value = null, $args = [])
    {
        if ($value && $value->getKind() == TypeMap::KIND_ENUM) {
            $data = [];
            foreach ($value->getValues() as $value) {
                if(!array_key_exists('description', $value)){
                    $value['description'] = '';
                }
                if(!array_key_exists('isDeprecated', $value)){
                    $value['isDeprecated'] = false;
                }
                if(!array_key_exists('deprecationReason', $value)){
                    $value['deprecationReason'] = '';
                }

                $data[] = $value;
            }

            return $data;
        }

        return null;
    }
}
