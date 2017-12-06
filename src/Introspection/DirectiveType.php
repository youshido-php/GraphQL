<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Config\Directive\DirectiveConfig;
use Youshido\GraphQL\Directive\Directive;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class DirectiveType
 */
class DirectiveType extends AbstractObjectType
{
    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Directive';
    }

    /**
     * @param DirectiveInterface $value
     *
     * @return array|\Youshido\GraphQL\Type\AbstractType[]
     */
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

        return $directiveConfig->getLocations();
    }

    /**
     * @param \Youshido\GraphQL\Config\Object\ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('args', [
                'type'    => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => [$this, 'resolveArgs'],
            ])
            ->addField('locations', [
                'type'    => new NonNullType(new ListType(new NonNullType(new DirectiveLocationType()))),
                'resolve' => [$this, 'resolveLocations'],
            ]);
    }
}
