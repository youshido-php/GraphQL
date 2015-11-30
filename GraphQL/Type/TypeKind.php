<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 12:45 AM
*/

namespace Youshido\GraphQL\Type;


class TypeKind
{
    const KIND_SCALAR = 'SCALAR';
    const KIND_OBJECT = 'OBJECT';
    const KIND_LIST   = 'LIST';
    const KIND_ENUM   = 'ENUM';
    const KIND_UNION  = 'UNION';
}