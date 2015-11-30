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
    const ScalarKind = 'SCALAR';
    const ObjectKind = 'OBJECT';
    const ListKind   = 'LIST';
    const EnumKind   = 'ENUM';
    const UnionKind  = 'UNION';
}