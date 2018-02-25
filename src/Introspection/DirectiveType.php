<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Config\Directive\DirectiveConfig;
use Youshido\GraphQL\Directive\Directive;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class DirectiveType extends AbstractObjectType
{

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Directive';
    }

    public function resolveArgs(DirectiveInterface $value)
    {
        if ($value->hasArguments()) {
            return $value->getArguments();
        }

        return [];
    }

    /**
     * @param DirectiveInterface|Directive $value
     *
     * @return mixed
     */
    public function resolveLocations(DirectiveInterface $value)
    {
        /** @var DirectiveConfig $directiveConfig */
        $directiveConfig = $value->getConfig();

        $locations = $directiveConfig->getLocations();

        return $locations;
    }

    public function build($config)
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('args', [
                'type'    => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => [$this, 'resolveArgs'],
            ])
            ->addField('locations',[
                'type'  =>  new NonNullType(new ListType(new NonNullType(new DirectiveLocationType()))),
                'resolve' => [$this, 'resolveLocations'],
            ]);
    }
}
