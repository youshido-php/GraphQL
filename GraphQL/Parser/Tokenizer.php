<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser;

class Tokenizer
{
    protected $source;
    protected $pos = 0;
    protected $line = 1;
    protected $lineStart = 0;

    /** @var  Token */
    protected $lookAhead;

    public function setSource($source)
    {
        $this->source    = $source;
        $this->lookAhead = $this->next();
    }

    protected function next()
    {
        $this->skipWhitespace();

        $line      = $this->line;
        $lineStart = $this->lineStart;
        $token     = $this->scan();

        $token->line   = $line;
        $token->column = $this->pos - $lineStart;

        return $token;
    }

    protected function skipWhitespace()
    {
        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];
            if ($ch === ' ' || $ch === "\t") {
                $this->pos++;
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
     */
    protected function scan()
    {
        if ($this->pos >= strlen($this->source)) {
            return new Token(Token::TYPE_END);
        }

        $ch = $this->source[$this->pos];
        switch ($ch) {
            case Token::TYPE_LPAREN:
                ++$this->pos;

                return new Token(Token::TYPE_LPAREN);
            case Token::TYPE_RPAREN:
                ++$this->pos;

                return new Token(Token::TYPE_RPAREN);
            case Token::TYPE_LBRACE:
                ++$this->pos;

                return new Token(Token::TYPE_LBRACE);
            case Token::TYPE_RBRACE:
                ++$this->pos;

                return new Token(Token::TYPE_RBRACE);
            case Token::TYPE_LT:
                ++$this->pos;

                return new Token(Token::TYPE_LT);
            case Token::TYPE_GT:
                ++$this->pos;

                return new Token(Token::TYPE_GT);
            case Token::TYPE_AMP:
                ++$this->pos;

                return new Token(Token::TYPE_AMP);
            case Token::TYPE_COMMA:
                ++$this->pos;

                return new Token(Token::TYPE_COMMA);
            case Token::TYPE_LSQUARE_BRACE:
                ++$this->pos;

                return new Token(Token::TYPE_LSQUARE_BRACE);
            case Token::TYPE_RSQUARE_BRACE:
                ++$this->pos;

                return new Token(Token::TYPE_RSQUARE_BRACE);
            case Token::TYPE_COLON:
                ++$this->pos;

                return new Token(Token::TYPE_COLON);

            case Token::TYPE_POINT:
                if ($this->checkFragment()) {
                    return new Token(Token::TYPE_FRAGMENT_REFERENCE);
                }

                break;
        }

        if ($ch === '_' || $ch === '$' || 'a' <= $ch && $ch <= 'z' || 'A' <= $ch && $ch <= 'Z') {
            return $this->scanWord();
        }

        if ($ch === '-' || '0' <= $ch && $ch <= '9') {
            return $this->scanNumber();
        }

        if ($ch === '"') {
            return $this->scanString();
        }

        throw $this->createIllegal();
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

            if ($ch === '_' || $ch === '$' || 'a' <= $ch && $ch <= ('z') || 'A' <= $ch && $ch <= 'Z' || '0' <= $ch && $ch <= '9') {
                $this->pos++;
            } else {
                break;
            }
        }

        $value = substr($this->source, $start, $this->pos - $start);

        return new Token($this->getKeyword($value), $value);
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

            case 'as':
                return Token::TYPE_AS;
        }

        return Token::TYPE_IDENTIFIER;
    }

    protected function scanNumber()
    {
        $start = $this->pos;

        if ($this->source[$this->pos] === '-') {
            $this->pos++;
        }

        $this->skipInteger();

        if ($this->source[$this->pos] === '->') {
            $this->pos++;
            $this->skipInteger();
        }

        $ch = $this->source[$this->pos];
        if ($ch === 'e' || $ch === 'E') {
            $this->pos++;

            $ch = $this->source[$this->pos];
            if ($ch === '+' || $ch === '-') {
                $this->pos++;
            }

            $this->skipInteger();
        }

        $value = (float)substr($this->source, $start, $this->pos);

        return new Token(Token::TYPE_NUMBER, $value);
    }

    protected function skipInteger()
    {
        $start = $this->pos;

        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];
            if ('0' <= $ch && $ch <= '9') {
                $this->pos++;
            } else {
                break;
            }
        }

        if ($this->pos - $start === 0) {
            throw $this->createIllegal();
        }
    }

    protected function createIllegal()
    {
        return $this->pos < strlen($this->source)
            ? $this->createError("Unexpected {$this->source[$this->pos]}")
            : $this->createError('Unexpected end of input');
    }

    protected function createError($message)
    {
        return new SyntaxErrorException($message . " ({$this->line}:{$this->getColumn()})");
    }

    protected function getColumn()
    {
        return $this->pos - $this->lineStart;
    }

    protected function scanString()
    {
        $this->pos++;

        $value = '';
        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];
            if ($ch === '"') {
                $this->pos++;

                return new Token(Token::TYPE_STRING, $value);
            }

            if ($ch === "\r" || $ch === "\n") {
                break;
            }

            $value .= $ch;
            $this->pos++;
        }

        throw $this->createIllegal();
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

    protected function createUnexpected(Token $token)
    {
        switch ($token) {
            case Token::TYPE_END:
                return $this->createError('Unexpected end of input');
            case Token::TYPE_NUMBER:
                return $this->createError('Unexpected number');
            case Token::TYPE_STRING:
                return $this->createError('Unexpected string');
            case Token::TYPE_IDENTIFIER:
                return $this->createError('Unexpected identifier');
        }

        return new \Exception('Unexpected token');
    }
}