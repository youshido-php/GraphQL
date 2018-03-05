<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11/30/15 12:36 AM
 */

namespace Youshido\GraphQL\Type;

class TypeMap
{
    public const KIND_SCALAR = 'SCALAR';

    public const KIND_OBJECT = 'OBJECT';

    public const KIND_INTERFACE = 'INTERFACE';

    public const KIND_UNION = 'UNION';

    public const KIND_ENUM = 'ENUM';

    public const KIND_INPUT_OBJECT = 'INPUT_OBJECT';

    public const KIND_LIST = 'LIST';

    public const KIND_NON_NULL = 'NON_NULL';

    public const TYPE_INT = 'int';

    public const TYPE_FLOAT = 'float';

    public const TYPE_STRING = 'string';

    public const TYPE_BOOLEAN = 'boolean';

    public const TYPE_ID = 'id';

    public const TYPE_DATETIME = 'datetime';

    public const TYPE_DATETIMETZ = 'datetimetz';

    public const TYPE_DATE = 'date';

    public const TYPE_TIMESTAMP = 'timestamp';
}
