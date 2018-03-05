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
/**
 * Date: 23.11.15.
 */

namespace Youshido\GraphQL\Parser;

class Token
{
    public const TYPE_END = 'end';

    public const TYPE_IDENTIFIER = 'identifier';

    public const TYPE_NUMBER = 'number';

    public const TYPE_STRING = 'string';

    public const TYPE_ON = 'on';

    public const TYPE_QUERY = 'query';

    public const TYPE_MUTATION = 'mutation';

    public const TYPE_FRAGMENT = 'fragment';

    public const TYPE_FRAGMENT_REFERENCE = '...';

    public const TYPE_TYPED_FRAGMENT = 'typed fragment';

    public const TYPE_LBRACE = '{';

    public const TYPE_RBRACE = '}';

    public const TYPE_LPAREN = '(';

    public const TYPE_RPAREN = ')';

    public const TYPE_LSQUARE_BRACE = '[';

    public const TYPE_RSQUARE_BRACE = ']';

    public const TYPE_COLON = ':';

    public const TYPE_COMMA = ',';

    public const TYPE_VARIABLE = '$';

    public const TYPE_POINT = '.';

    public const TYPE_REQUIRED = '!';

    public const TYPE_EQUAL = '=';

    public const TYPE_AT = '@';

    public const TYPE_NULL = 'null';

    public const TYPE_TRUE = 'true';

    public const TYPE_FALSE = 'false';

    /** @var mixed */
    private $data;

    /** @var string */
    private $type;

    /** @var int */
    private $line;

    /** @var int */
    private $column;

    public function __construct($type, $line, $column, $data = null)
    {
        $this->type = $type;
        $this->data = $data;

        $this->line   = $line;
        $this->column = $column;

        if ($data) {
            $tokenLength = \mb_strlen($data);
            $tokenLength = $tokenLength > 1 ? $tokenLength - 1 : 0;

            $this->column = $column - $tokenLength;
        }

        if (self::TYPE_TRUE === $this->getType()) {
            $this->data = true;
        }

        if (self::TYPE_FALSE === $this->getType()) {
            $this->data = false;
        }

        if (self::TYPE_NULL === $this->getType()) {
            $this->data = null;
        }
    }

    public static function tokenName($tokenType)
    {
        return [
            self::TYPE_END                => 'END',
            self::TYPE_IDENTIFIER         => 'IDENTIFIER',
            self::TYPE_NUMBER             => 'NUMBER',
            self::TYPE_STRING             => 'STRING',
            self::TYPE_ON                 => 'ON',
            self::TYPE_QUERY              => 'QUERY',
            self::TYPE_MUTATION           => 'MUTATION',
            self::TYPE_FRAGMENT           => 'FRAGMENT',
            self::TYPE_FRAGMENT_REFERENCE => 'FRAGMENT_REFERENCE',
            self::TYPE_TYPED_FRAGMENT     => 'TYPED_FRAGMENT',
            self::TYPE_LBRACE             => 'LBRACE',
            self::TYPE_RBRACE             => 'RBRACE',
            self::TYPE_LPAREN             => 'LPAREN',
            self::TYPE_RPAREN             => 'RPAREN',
            self::TYPE_LSQUARE_BRACE      => 'LSQUARE_BRACE',
            self::TYPE_RSQUARE_BRACE      => 'RSQUARE_BRACE',
            self::TYPE_COLON              => 'COLON',
            self::TYPE_COMMA              => 'COMMA',
            self::TYPE_VARIABLE           => 'VARIABLE',
            self::TYPE_POINT              => 'POINT',
            self::TYPE_NULL               => 'NULL',
            self::TYPE_TRUE               => 'TRUE',
            self::TYPE_FALSE              => 'FALSE',
            self::TYPE_REQUIRED           => 'REQUIRED',
            self::TYPE_AT                 => 'AT',
        ][$tokenType];
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }
}
