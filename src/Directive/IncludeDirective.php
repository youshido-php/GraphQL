<?php

namespace Youshido\GraphQL\Directive;

use Youshido\GraphQL\Parser\Ast\Interfaces\DirectivesContainerInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\BooleanType;

/**
 * Class IncludeDirective
 */
class IncludeDirective
{
    /**
     * @return Directive
     */
    public static function build()
    {
        return new Directive([
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
            ],
        ]);
    }

    /**
     * @param DirectivesContainerInterface $field
     *
     * @return bool
     */
    public static function check(DirectivesContainerInterface $field)
    {
        if ($directive = $field->getDirective('include')) {
            return !$directive->getArgument('if')->getValue();
        }

        return false;
    }
}
