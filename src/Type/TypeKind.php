<?php

namespace Youshido\GraphQL\Type;

/**
 * Class TypeKind
 */
class TypeKind
{
    const KIND_SCALAR       = 'SCALAR';
    const KIND_OBJECT       = 'OBJECT';
    const KIND_INTERFACE    = 'INTERFACE';
    const KIND_UNION        = 'UNION';
    const KIND_ENUM         = 'ENUM';
    const KIND_INPUT_OBJECT = 'INPUT_OBJECT';
    const KIND_LIST         = 'LIST';
    const KIND_NON_NULL     = 'NON_NULL';
}
