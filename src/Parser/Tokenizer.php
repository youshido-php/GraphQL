<?php

namespace Youshido\GraphQL\Parser;

use Youshido\GraphQL\Exception\Parser\SyntaxErrorException;

/**
 * Class Tokenizer
 */
class Tokenizer
{
    /** @var  string */
    protected $source;

    /** @var int */
    protected $pos = 0;

    /** @var int */
    protected $line = 1;

    /** @var int */
    protected $lineStart = 0;

    /** @var  Token */
    protected $lookAhead;

    /**
     * Tokenizer constructor.
     *
     * @param string $source
     */
    public function __construct($source)
    {
        $this->source    = $source;
        $this->lookAhead = $this->next();
    }

    /**
     * @param string $type
     *
     * @return Token
     * @throws SyntaxErrorException
     */
    public function expect($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        throw $this->createUnexpectedTokenTypeException($this->peek()->getType());
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function match($type)
    {
        return $this->peek()->getType() === $type;
    }

    /**
     * @return bool
     */
    public function end()
    {
        return $this->lookAhead->getType() === Token::TYPE_END;
    }

    /**
     * @return Token
     */
    public function peek()
    {
        return $this->lookAhead;
    }

    /**
     * @return Token
     */
    public function lex()
    {
        $prev            = $this->lookAhead;
        $this->lookAhead = $this->next();

        return $prev;
    }

    /**
     * @param string $type
     *
     * @return null|Token
     */
    public function eat($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        return null;
    }

    /**
     * @param array $types
     *
     * @return null|Token
     */
    public function eatMulti($types)
    {
        if ($this->matchMulti($types)) {
            return $this->lex();
        }

        return null;
    }

    /**
     * @param array $types
     *
     * @return bool
     */
    public function matchMulti($types)
    {
        foreach ((array) $types as $type) {
            if ($this->peek()->getType() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $types
     *
     * @return Token
     * @throws SyntaxErrorException
     */
    public function expectMulti($types)
    {
        if ($this->matchMulti($types)) {
            return $this->lex();
        }

        throw $this->createUnexpectedTokenTypeException($this->peek()->getType());
    }

    /**
     * @param string $message
     *
     * @return SyntaxErrorException
     */
    public function createException($message)
    {
        return new SyntaxErrorException($message, $this->getLocation());
    }

    /**
     * @param string $tokenType
     *
     * @return SyntaxErrorException
     */
    public function createUnexpectedTokenTypeException($tokenType)
    {
        return $this->createException(sprintf('Unexpected token "%s"', Token::tokenName($tokenType)));
    }

    /**
     * @return Token
     */
    private function next()
    {
        $this->skipWhitespace();

        return $this->scan();
    }

    private function getKeyword($name)
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

            default:
                return Token::TYPE_IDENTIFIER;
        }
    }

    private function scan()
    {
        if ($this->pos >= strlen($this->source)) {
            return new Token(Token::TYPE_END, $this->getLocation());
        }

        $ch = $this->source[$this->pos];
        switch ($ch) {
            case Token::TYPE_LPAREN:
                ++$this->pos;

                return new Token(Token::TYPE_LPAREN, $this->getLocation());
            case Token::TYPE_RPAREN:
                ++$this->pos;

                return new Token(Token::TYPE_RPAREN, $this->getLocation());
            case Token::TYPE_LBRACE:
                ++$this->pos;

                return new Token(Token::TYPE_LBRACE, $this->getLocation());
            case Token::TYPE_RBRACE:
                ++$this->pos;

                return new Token(Token::TYPE_RBRACE, $this->getLocation());
            case Token::TYPE_COMMA:
                ++$this->pos;

                return new Token(Token::TYPE_COMMA, $this->getLocation());
            case Token::TYPE_LSQUARE_BRACE:
                ++$this->pos;

                return new Token(Token::TYPE_LSQUARE_BRACE, $this->getLocation());
            case Token::TYPE_RSQUARE_BRACE:
                ++$this->pos;

                return new Token(Token::TYPE_RSQUARE_BRACE, $this->getLocation());
            case Token::TYPE_REQUIRED:
                ++$this->pos;

                return new Token(Token::TYPE_REQUIRED, $this->getLocation());
            case Token::TYPE_AT:
                ++$this->pos;

                return new Token(Token::TYPE_AT, $this->getLocation());
            case Token::TYPE_COLON:
                ++$this->pos;

                return new Token(Token::TYPE_COLON, $this->getLocation());

            case Token::TYPE_EQUAL:
                ++$this->pos;

                return new Token(Token::TYPE_EQUAL, $this->getLocation());

            case Token::TYPE_POINT:
                if ($this->checkFragment()) {
                    return new Token(Token::TYPE_FRAGMENT_REFERENCE, $this->getLocation());
                }

                return new Token(Token::TYPE_POINT, $this->getLocation());


            case Token::TYPE_VARIABLE:
                ++$this->pos;

                return new Token(Token::TYPE_VARIABLE, $this->getLocation());
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

    private function checkFragment()
    {
        $this->pos++;
        $ch = $this->source[$this->pos];

        $this->pos++;
        $nextCh = $this->source[$this->pos];

        if ($ch === Token::TYPE_POINT && $nextCh === Token::TYPE_POINT) {
            $this->pos++;

            return true;
        }

        return false;
    }

    private function skipWhitespace()
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

    private function scanWord()
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

        return new Token($this->getKeyword($value), $this->getLocation(), $value);
    }

    private function scanNumber()
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

        return new Token(Token::TYPE_NUMBER, $this->getLocation(), $value);
    }

    private function skipInteger()
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

    private function scanString()
    {
        $this->pos++;

        $value = '';
        while ($this->pos < strlen($this->source)) {
            $ch = $this->source[$this->pos];
            if ($ch === '"' && $this->source[$this->pos - 1] !== '\\') {
                $token = new Token(Token::TYPE_STRING, $this->getLocation(), $value);
                $this->pos++;

                return $token;
            }

            $value .= $ch;
            $this->pos++;
        }

        throw $this->createUnexpectedTokenTypeException(Token::TYPE_END);
    }

    private function getLocation()
    {
        return new Location($this->line, $this->pos - $this->lineStart);
    }
}
