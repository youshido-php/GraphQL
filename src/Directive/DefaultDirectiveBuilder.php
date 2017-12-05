<?php

namespace Youshido\GraphQL\Directive;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\BooleanType;

/**
 * Class DefaultDirectiveBuilder
 */
class DefaultDirectiveBuilder
{
    /**
     * @return DirectiveInterface[]
     */
    public static function build()
    {
        return [
            new Directive([
                'name'        => 'include',
                'description' => 'Directs the executor to include this field or fragment only when the `if` argument is true.',
                'args'        => [
                    'if' => [
                        'type'        => new NonNullType(new BooleanType()),
                        'description' => 'Included when true.',
                    ],
                ],
                'locations'   => [
                    DirectiveLocation::FIELD,
                    DirectiveLocation::FRAGMENT_SPREAD,
                    DirectiveLocation::INLINE_FRAGMENT,

                    DirectiveLocation::FRAGMENT_DEFINITION,
                    DirectiveLocation::MUTATION,
                    DirectiveLocation::QUERY,
                ],
            ]),
            new Directive([
                'name'        => 'skip',
                'description' => 'Directs the executor to skip this field or fragment when the `if` argument is true.',
                'args'        => [
                    'if' => [
                        'type'        => new NonNullType(new BooleanType()),
                        'description' => 'Skipped when true.',
                    ],
                ],
                'locations'   => [
                    DirectiveLocation::FIELD,
                    DirectiveLocation::FRAGMENT_SPREAD,
                    DirectiveLocation::INLINE_FRAGMENT,
                ],
            ]),
        ];
    }
}
