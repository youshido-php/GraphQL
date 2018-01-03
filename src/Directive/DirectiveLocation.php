<?php

namespace Youshido\GraphQL\Directive;

/**
 * Class DirectiveLocation
 */
class DirectiveLocation
{
    const QUERY               = 'QUERY';
    const MUTATION            = 'MUTATION';
    const FIELD               = 'FIELD';
    const FRAGMENT_DEFINITION = 'FRAGMENT_DEFINITION';
    const FRAGMENT_SPREAD     = 'FRAGMENT_SPREAD';
    const INLINE_FRAGMENT     = 'INLINE_FRAGMENT';

    public static $locations = [
        self::QUERY,
        self::MUTATION,
        self::FIELD,
        self::FRAGMENT_DEFINITION,
        self::FRAGMENT_SPREAD,
        self::INLINE_FRAGMENT,
    ];
}
