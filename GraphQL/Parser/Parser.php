<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* created: 11/23/15 1:22 AM
*/

namespace Youshido\GraphQL\Parser;

use Youshido\GraphQL\Request;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Literal;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\Reference;
use Youshido\GraphQL\Parser\Ast\Variable;

class Parser extends Tokenizer
{

    public function parse()
    {
        $data = ['queries' => [], 'mutations' => [], 'fragments' => []];

        while (!$this->end()) {
            $tokenType = $this->getCurrentTokenType();

            switch ($tokenType) {
                case Token::TYPE_LBRACE:
                case Token::TYPE_QUERY:
                    $data = array_merge($data, ['queries' => $this->parseBody()]);
                    break;

                case Token::TYPE_MUTATION:
                    $data = array_merge($data, ['mutations' => $this->parseBody(Token::TYPE_MUTATION)]);
                    break;

                case Token::TYPE_FRAGMENT:
                    $data['fragments'][] = $this->parseFragment();
                    break;
            }
        }

        return $data;
    }

    protected function getCurrentTokenType()
    {
        return $this->lookAhead->getType();
    }

    protected function parseBody($token = Token::TYPE_QUERY)
    {
        $fields = [];
        $first  = true;

        if ($this->getCurrentTokenType() == $token) {
            $this->lex();
        }

        $this->lex();

        while (!$this->match(Token::TYPE_RBRACE) && !$this->end()) {
            if ($first) {
                $first = false;
            } else {
                $this->expect(Token::TYPE_COMMA);
            }

            if ($this->match(Token::TYPE_AMP)) {
                $fields[] = $this->parseReference();
            } elseif ($this->match(Token::TYPE_FRAGMENT_REFERENCE)) {
                $this->lex();

                $fields[] = $this->parseFragmentReference();
            } else {
                $fields[] = $this->parseBodyItem($token);
            }
        }

        $this->expect(Token::TYPE_RBRACE);

        return $fields;
    }

    protected function expect($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        throw $this->createUnexpected($this->lookAhead);
    }

    protected function parseReference()
    {
        $this->expect(Token::TYPE_AMP);

        if ($this->match(Token::TYPE_NUMBER) || $this->match(Token::TYPE_IDENTIFIER)) {
            return new Reference($this->lex()->getData());
        }

        throw $this->createUnexpected($this->lookAhead);
    }

    protected function parseFragmentReference()
    {
        $name = $this->parseIdentifier();

        return new FragmentReference($name);
    }

    protected function parseIdentifier()
    {
        return $this->expect(Token::TYPE_IDENTIFIER)->getData();
    }

    protected function parseBodyItem($type = Token::TYPE_QUERY)
    {
        $name  = $this->parseIdentifier();
        $alias = null;

        if ($this->eat(Token::TYPE_COLON)) {
            $alias = $name;
            $name  = $this->parseIdentifier();
        }

        $params = $this->match(Token::TYPE_LPAREN) ? $this->parseArgumentList() : [];

        if ($this->match(Token::TYPE_LBRACE)) {
            $fields = $this->parseBody();

            if ($type == Token::TYPE_QUERY) {
                return new Query($name, $alias, $params, $fields);
            } else {
                return new Mutation($name, $alias, $params, $fields);
            }
        } else {
            return new Field($name, $alias);
        }
    }

    protected function parseArgumentList()
    {
        $args  = [];
        $first = true;

        $this->expect(Token::TYPE_LPAREN);

        while (!$this->match(Token::TYPE_RPAREN) && !$this->end()) {
            if ($first) {
                $first = false;
            } else {
                $this->expect(Token::TYPE_COMMA);
            }

            $args[] = $this->parseArgument();
        }

        $this->expect(Token::TYPE_RPAREN);

        return $args;
    }

    protected function parseArgument()
    {
        $name = $this->parseIdentifier();
        $this->expect(Token::TYPE_COLON);
        $value = $this->parseValue();

        return new Argument($name, $value);
    }

    protected function parseValue()
    {
        switch ($this->lookAhead->getType()) {
            case Token::TYPE_AMP:
                return $this->parseReference();

            case Token::TYPE_LT:
                return $this->parseVariable();

            case Token::TYPE_NUMBER:
            case Token::TYPE_STRING:
                return new Literal($this->lex()->getData());

            case Token::TYPE_NULL:
            case Token::TYPE_TRUE:
            case Token::TYPE_FALSE:
                return new Literal(json_encode($this->lex()->getData()));
        }

        throw $this->createUnexpected($this->lookAhead);
    }

    protected function parseVariable()
    {
        $this->expect(Token::TYPE_LT);
        $name = $this->expect(Token::TYPE_IDENTIFIER)->getData();
        $this->expect(Token::TYPE_GT);

        return new Variable($name);
    }

    protected function parseFragment()
    {
        $this->lex();
        $name = $this->parseIdentifier();

        $this->eat(Token::TYPE_ON);

        $model  = $this->parseIdentifier();
        $fields = $this->parseBody();

        return new Fragment($name, $model, $fields);
    }

    protected function eatIdentifier()
    {
        $token = $this->eat(Token::TYPE_IDENTIFIER);

        return $token ? $token->getData() : null;
    }

    protected function eat($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        return null;
    }

    protected function match($type)
    {
        return $this->lookAhead->getType() === $type;
    }
}