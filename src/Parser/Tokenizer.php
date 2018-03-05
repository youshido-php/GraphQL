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

use Youshido\GraphQL\Exception\Parser\SyntaxErrorException;

class Tokenizer
{
    protected $source;

    protected $pos = 0;

    protected $line = 1;

    protected $lineStart = 0;

    /** @var Token */
    protected $lookAhead;

    protected function initTokenizer($source): void
    {
        $this->source    = $source;
        $this->lookAhead = $this->next();
    }

    /**
     * @return Token
     */
    protected function next()
    {
        $this->skipWhitespace();

        return $this->scan();
    }

    protected function skipWhitespace(): void
    {
        while ($this->pos < \mb_strlen($this->source)) {
            $ch = $this->source[$this->pos];

            if (' ' === $ch || "\t" === $ch || ',' === $ch) {
                $this->pos++;

                continue;
            }

            if ('#' === $ch) {
                $this->pos++;

                while ($this->pos < \mb_strlen($this->source) && ($code = \ord($this->source[$this->pos])) && 10 !== $code && 13 !== $code && 0x2028 !== $code && 0x2029 !== $code
                ) {
                    $this->pos++;
                }

                continue;
            }

            if ("\r" === $ch) {
                $this->pos++;

                if ("\n" === $this->source[$this->pos]) {
                    $this->pos++;
                }
                $this->line++;
                $this->lineStart = $this->pos;

                continue;
            }

            if ("\n" === $ch) {
                $this->pos++;
                $this->line++;
                $this->lineStart = $this->pos;

                continue;
            }

            break;
        }
    }

    /**
     * @throws SyntaxErrorException
     *
     * @return Token
     */
    protected function scan()
    {
        if ($this->pos >= \mb_strlen($this->source)) {
            return new Token(Token::TYPE_END, $this->getLine(), $this->getColumn());
        }

        $ch = $this->source[$this->pos];
        switch ($ch) {
            case Token::TYPE_LPAREN:
                ++$this->pos;

                return new Token(Token::TYPE_LPAREN, $this->getLine(), $this->getColumn());
            case Token::TYPE_RPAREN:
                ++$this->pos;

                return new Token(Token::TYPE_RPAREN, $this->getLine(), $this->getColumn());
            case Token::TYPE_LBRACE:
                ++$this->pos;

                return new Token(Token::TYPE_LBRACE, $this->getLine(), $this->getColumn());
            case Token::TYPE_RBRACE:
                ++$this->pos;

                return new Token(Token::TYPE_RBRACE, $this->getLine(), $this->getColumn());
            case Token::TYPE_COMMA:
                ++$this->pos;

                return new Token(Token::TYPE_COMMA, $this->getLine(), $this->getColumn());
            case Token::TYPE_LSQUARE_BRACE:
                ++$this->pos;

                return new Token(Token::TYPE_LSQUARE_BRACE, $this->getLine(), $this->getColumn());
            case Token::TYPE_RSQUARE_BRACE:
                ++$this->pos;

                return new Token(Token::TYPE_RSQUARE_BRACE, $this->getLine(), $this->getColumn());
            case Token::TYPE_REQUIRED:
                ++$this->pos;

                return new Token(Token::TYPE_REQUIRED, $this->getLine(), $this->getColumn());
            case Token::TYPE_AT:
                ++$this->pos;

                return new Token(Token::TYPE_AT, $this->getLine(), $this->getColumn());
            case Token::TYPE_COLON:
                ++$this->pos;

                return new Token(Token::TYPE_COLON, $this->getLine(), $this->getColumn());

            case Token::TYPE_EQUAL:
                ++$this->pos;

                return new Token(Token::TYPE_EQUAL, $this->getLine(), $this->getColumn());

            case Token::TYPE_POINT:
                if ($this->checkFragment()) {
                    return new Token(Token::TYPE_FRAGMENT_REFERENCE, $this->getLine(), $this->getColumn());
                }

                return new Token(Token::TYPE_POINT, $this->getLine(), $this->getColumn());

            case Token::TYPE_VARIABLE:
                ++$this->pos;

                return new Token(Token::TYPE_VARIABLE, $this->getLine(), $this->getColumn());
        }

        if ('_' === $ch || ($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z')) {
            return $this->scanWord();
        }

        if ('-' === $ch || ($ch >= '0' && $ch <= '9')) {
            return $this->scanNumber();
        }

        if ('"' === $ch) {
            return $this->scanString();
        }

        throw $this->createException('Can\t recognize token type');
    }

    protected function checkFragment()
    {
        $this->pos++;
        $ch = $this->source[$this->pos];

        $this->pos++;
        $nextCh = $this->source[$this->pos];

        if (Token::TYPE_POINT === $ch && Token::TYPE_POINT === $nextCh) {
            $this->pos++;

            return true;
        }

        return false;
    }

    protected function scanWord()
    {
        $start = $this->pos;
        $this->pos++;

        while ($this->pos < \mb_strlen($this->source)) {
            $ch = $this->source[$this->pos];

            if ('_' === $ch || '$' === $ch || ($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z') || ($ch >= '0' && $ch <= '9')) {
                $this->pos++;

                continue;
            }

            break;
        }

        $value = \mb_substr($this->source, $start, $this->pos - $start);

        return new Token($this->getKeyword($value), $this->getLine(), $this->getColumn(), $value);
    }

    protected function getKeyword($name)
    {
        switch ($name) {
            case 'null':
                return Token::TYPE_NULL;

            case 'true':
                return Token::TYPE_TRUE;

            case 'false':
                return Token::TYPE_FALSE;

            case 'query':
                return Token::TYPE_QUERY;

            case 'fragment':
                return Token::TYPE_FRAGMENT;

            case 'mutation':
                return Token::TYPE_MUTATION;

            case 'on':
                return Token::TYPE_ON;
        }

        return Token::TYPE_IDENTIFIER;
    }

    protected function expect($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        throw $this->createUnexpectedException($this->peek());
    }

    protected function match($type)
    {
        return $this->peek()->getType() === $type;
    }

    protected function scanNumber()
    {
        $start = $this->pos;

        if ('-' === $this->source[$this->pos]) {
            $this->pos++;
        }

        $this->skipInteger();

        if (isset($this->source[$this->pos]) && '.' === $this->source[$this->pos]) {
            $this->pos++;
            $this->skipInteger();
        }

        $value = \mb_substr($this->source, $start, $this->pos - $start);

        if (false === \mb_strpos($value, '.')) {
            $value = (int) $value;
        } else {
            $value = (float) $value;
        }

        return new Token(Token::TYPE_NUMBER, $this->getLine(), $this->getColumn(), $value);
    }

    protected function skipInteger(): void
    {
        while ($this->pos < \mb_strlen($this->source)) {
            $ch = $this->source[$this->pos];

            if ($ch >= '0' && $ch <= '9') {
                $this->pos++;

                continue;
            }

            break;
        }
    }

    protected function createException($message)
    {
        return new SyntaxErrorException(\sprintf('%s', $message), $this->getLocation());
    }

    protected function getLocation()
    {
        return new Location($this->getLine(), $this->getColumn());
    }

    protected function getColumn()
    {
        return $this->pos - $this->lineStart;
    }

    protected function getLine()
    {
        return $this->line;
    }

    /*
        http://facebook.github.io/graphql/October2016/#sec-String-Value
     */
    protected function scanString()
    {
        $len = \mb_strlen($this->source);
        $this->pos++;

        $value = '';

        while ($this->pos < $len) {
            $ch = $this->source[$this->pos];

            if ('"' === $ch) {
                $token = new Token(Token::TYPE_STRING, $this->getLine(), $this->getColumn(), $value);
                $this->pos++;

                return $token;
            }

            if ('\\' === $ch && ($this->pos < ($len - 1))) {
                $this->pos++;
                $ch = $this->source[$this->pos];
                switch ($ch) {
                    case '"':
                    case '\\':
                    case '/':
                        break;
                    case 'b':
                        $ch = \sprintf('%c', 8);

                        break;
                    case 'f':
                        $ch = "\f";

                        break;
                    case 'n':
                        $ch = "\n";

                        break;
                    case 'r':
                        $ch = "\r";

                        break;
                    case 'u':
                        $codepoint = \mb_substr($this->source, $this->pos + 1, 4);

                        if (!\preg_match('/[0-9A-Fa-f]{4}/', $codepoint)) {
                            throw $this->createException(\sprintf('Invalid string unicode escape sequece "%s"', $codepoint));
                        }
                        $ch = \html_entity_decode("&#x{$codepoint};", ENT_QUOTES, 'UTF-8');
                        $this->pos += 4;

                        break;
                    default:
                        throw $this->createException(\sprintf('Unexpected string escaped character "%s"', $ch));

                        break;

                }
            }

            $value .= $ch;
            $this->pos++;
        }

        throw $this->createUnexpectedTokenTypeException(Token::TYPE_END);
    }

    protected function end()
    {
        return Token::TYPE_END === $this->lookAhead->getType();
    }

    protected function peek()
    {
        return $this->lookAhead;
    }

    protected function lex()
    {
        $prev            = $this->lookAhead;
        $this->lookAhead = $this->next();

        return $prev;
    }

    protected function createUnexpectedException(Token $token)
    {
        return $this->createUnexpectedTokenTypeException($token->getType());
    }

    protected function createUnexpectedTokenTypeException($tokenType)
    {
        return $this->createException(\sprintf('Unexpected token "%s"', Token::tokenName($tokenType)));
    }
}
