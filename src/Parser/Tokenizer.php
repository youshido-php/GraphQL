<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser;

use Youshido\GraphQL\Exception\Parser\SyntaxErrorException;

class Tokenizer
{
    protected $source;
    protected $pos = 0;
    protected $line = 1;
    protected $lineStart = 0;

    /** @var  Token */
    protected $lookAhead;

    protected function initTokenizer($source)
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

    protected function skipWhitespace()
    {
        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];
            if ($ch === ' ' || $ch === "\t" || $ch === ',') {
                $this->pos++;
            } elseif ($ch === '#') {
                $this->pos++;
                while (
                    $this->pos < strlen($this->source) &&
                    ($code = ord($this->source[$this->pos])) &&
                    $code !== 10 && $code !== 13 && $code !== 0x2028 && $code !== 0x2029
                ) {
                    $this->pos++;
                }
            } elseif ($ch === "\r") {
                $this->pos++;
                if ($this->source[$this->pos] === "\n") {
                    $this->pos++;
                }
                $this->line++;
                $this->lineStart = $this->pos;
            } elseif ($ch === "\n") {
                $this->pos++;
                $this->line++;
                $this->lineStart = $this->pos;
            } else {
                break;
            }
        }
    }

    /**
     * @return Token
     *
     * @throws SyntaxErrorException
     */
    protected function scan()
    {
        if ($this->pos >= strlen($this->source)) {
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

        if ($ch === '_' || ('a' <= $ch && $ch <= 'z') || ('A' <= $ch && $ch <= 'Z')) {
            return $this->scanWord();
        }

        if ($ch === '-' || ('0' <= $ch && $ch <= '9')) {
            return $this->scanNumber();
        }

        if ($ch === '"') {
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

        $isset = $ch == Token::TYPE_POINT && $nextCh == Token::TYPE_POINT;

        if ($isset) {
            $this->pos++;

            return true;
        }

        return false;
    }

    protected function scanWord()
    {
        $start = $this->pos;
        $this->pos++;

        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];

            if ($ch === '_' || $ch === '$' || ('a' <= $ch && $ch <= 'z') || ('A' <= $ch && $ch <= 'Z') || ('0' <= $ch && $ch <= '9')) {
                $this->pos++;
            } else {
                break;
            }
        }

        $value = substr($this->source, $start, $this->pos - $start);

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
        if ($this->source[$this->pos] === '-') {
            ++$this->pos;
        }

        $this->skipInteger();

        if (isset($this->source[$this->pos]) && $this->source[$this->pos] === '.') {
            $this->pos++;
            $this->skipInteger();
        }

        $value = substr($this->source, $start, $this->pos - $start);

        if (strpos($value, '.') === false) {
            $value = (int) $value;
        } else {
            $value = (float) $value;
        }

        return new Token(Token::TYPE_NUMBER, $this->getLine(), $this->getColumn(), $value);
    }

    protected function skipInteger()
    {
        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];
            if ('0' <= $ch && $ch <= '9') {
                $this->pos++;
            } else {
                break;
            }
        }
    }

    protected function createException($message)
    {
        return new SyntaxErrorException(sprintf('%s', $message), $this->getLocation());
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

    protected function scanString()
    {
        $this->pos++;

        $value = '';
        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];
            if ($ch === '"' && $this->source[$this->pos - 1] !== '\\') {
                $token = new Token(Token::TYPE_STRING, $this->getLine(), $this->getColumn(), $value);
                $this->pos++;

                return $token;
            }

            $value .= $ch;
            $this->pos++;
        }

        throw $this->createUnexpectedTokenTypeException(Token::TYPE_END);
    }

    protected function end()
    {
        return $this->lookAhead->getType() === Token::TYPE_END;
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
        return $this->createException(sprintf('Unexpected token "%s"', Token::tokenName($tokenType)));
    }
}
