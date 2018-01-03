<?php

namespace Youshido\GraphQL\Validator\ConfigValidator;

/**
 * Class PropertyType
 */
class PropertyType
{
    const TYPE_STRING       = 'string';
    const TYPE_LOCATION     = 'location';
    const TYPE_INPUT_FIELD  = 'input_field';
    const TYPE_GRAPHQL_TYPE = 'graphql_type';
    const TYPE_CALLABLE     = 'callable';
    const TYPE_BOOLEAN      = 'boolean';
    const TYPE_INT          = 'int';
    const TYPE_INPUT_TYPE   = 'input_type';
    const TYPE_ANY          = 'any';
    const TYPE_ENUM_VALUES  = 'enum_values';
    const TYPE_FIELD        = 'field';
    const TYPE_INTERFACE    = 'interface';
    const TYPE_OBJECT_TYPE  = 'object_type';
    const TYPE_DIRECTIVE    = 'directive';
    const TYPE_COST         = 'cost';
}
