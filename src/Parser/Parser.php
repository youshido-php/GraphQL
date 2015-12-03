<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* created: 11/23/15 1:22 AM
*/

namespace Youshido\GraphQL\Parser;

use Youshido\GraphQL\Parser\Value\InputList;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Value\InputObject;
use Youshido\GraphQL\Parser\Value\Literal;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\Reference;
use Youshido\GraphQL\Parser\Value\Variable;

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

    protected function parseBody($token = Token::TYPE_QUERY, $highLevel = true)
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
                $fields[] = $this->parseBodyItem($token, $highLevel);
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

    protected function parseBodyItem($type = Token::TYPE_QUERY, $highLevel = true)
    {
        $name  = $this->parseIdentifier();
        $alias = null;

        if ($this->eat(Token::TYPE_COLON)) {
            $alias = $name;
            $name  = $this->parseIdentifier();
        }

        $arguments = $this->match(Token::TYPE_LPAREN) ? $this->parseArgumentList() : [];

        if ($this->match(Token::TYPE_LBRACE)) {
            $fields = $this->parseBody($type, false);

            if ($type == Token::TYPE_QUERY) {
                return new Query($name, $alias, $arguments, $fields);
            } else {
                return new Mutation($name, $alias, $arguments, $fields);
            }
        } else {
            if($highLevel && $type == Token::TYPE_MUTATION){
                return new Mutation($name, $alias, $arguments);
            }

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
            case Token::TYPE_LSQUARE_BRACE:
                return $this->parseList();

            case Token::TYPE_LBRACE:
                return $this->parseObject();

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
                return new Literal($this->lex()->getData());
        }

        throw $this->createUnexpected($this->lookAhead);
    }

    protected function parseList($createType = true)
    {
        $this->eat(Token::TYPE_LSQUARE_BRACE);

        $list = [];
        while (!$this->match(Token::TYPE_RSQUARE_BRACE) && !$this->end()) {
            $list[] = $this->parseListValue();

            if ($this->lookAhead->getType() != Token::TYPE_RSQUARE_BRACE) {
                $this->expect(Token::TYPE_COMMA);
            }
        }

        $this->expect(Token::TYPE_RSQUARE_BRACE);

        return $createType ? new InputList($list) : $list;
    }

    protected function parseListValue()
    {
        switch ($this->lookAhead->getType()) {
            case Token::TYPE_NUMBER:
                return $this->expect(Token::TYPE_NUMBER)->getData();

            case Token::TYPE_STRING:
                return $this->expect(Token::TYPE_STRING)->getData();

            case Token::TYPE_LBRACE:
                return $this->parseObject(false);

            case Token::TYPE_LSQUARE_BRACE:
                return $this->parseList(false);

            case Token::TYPE_TRUE:
                return $this->expect(Token::TYPE_TRUE)->getData();

            case Token::TYPE_FALSE:
                return $this->expect(Token::TYPE_FALSE)->getData();

            case Token::TYPE_NULL:
                return $this->expect(Token::TYPE_NULL)->getData();
        }

        throw new \Exception('Can\'t parse argument');
    }

    protected function parseObject($createType = true)
    {
        $this->eat(Token::TYPE_LBRACE);

        $object = [];
        while (!$this->match(Token::TYPE_RBRACE) && !$this->end()) {
            $key = $this->expect(Token::TYPE_STRING)->getData();
            $this->expect(Token::TYPE_COLON);
            $value = $this->parseListValue();

            if ($this->lookAhead->getType() != Token::TYPE_RBRACE) {
                $this->expect(Token::TYPE_COMMA);
            }

            $object[$key] = $value;
        }

        $this->eat(Token::TYPE_RBRACE);

        return $createType ? new InputObject($object) : $object;
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