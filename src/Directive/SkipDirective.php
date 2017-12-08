<?php

namespace Youshido\GraphQL\Directive;

use Youshido\GraphQL\Parser\Ast\Interfaces\DirectivesContainerInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\BooleanType;

/**
 * Class SkipDirective
 */
class SkipDirective
{
    /**
     * @return Directive
     */
    public static function build()
    {
        return new Directive([
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
        ]);
    }

    /**
     * @param DirectivesContainerInterface $field
     *
     * @return bool
     */
    public static function check(DirectivesContainerInterface $field)
    {
        if ($directive = $field->getDirective('skip')) {
            return (bool) $directive->getArgument('if')->getValue();
        }

        return false;
    }
}
