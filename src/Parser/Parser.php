<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* created: 11/23/15 1:22 AM
*/

namespace Youshido\GraphQL\Parser;


use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;
use Youshido\GraphQL\Parser\Exception\DuplicationVariableException;
use Youshido\GraphQL\Parser\Exception\SyntaxErrorException;
use Youshido\GraphQL\Parser\Exception\UnusedVariableException;
use Youshido\GraphQL\Parser\Exception\VariableTypeNotDefined;
use Youshido\GraphQL\Parser\Value\InputList;
use Youshido\GraphQL\Parser\Value\InputObject;
use Youshido\GraphQL\Parser\Value\Literal;
use Youshido\GraphQL\Parser\Value\Variable;

class Parser extends Tokenizer
{

    /** @var array */
    protected $variablesTypes = [];

    /** @var array */
    protected $variableTypeUsage = [];

    public function parse($source = null)
    {
        if ($source) {
            $this->setSource($source);
        }
        $data = ['queries' => [], 'mutations' => [], 'fragments' => []];

        while (!$this->end()) {
            $tokenType = $this->peek()->getType();

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

                default:
                    throw new SyntaxErrorException();
            }
        }

        $this->checkVariableUsage();

        return $data;
    }

    protected function parseBody($token = Token::TYPE_QUERY, $highLevel = true)
    {
        $fields = [];
        $first  = true;

        if ($this->peek()->getType() == $token && $highLevel) {
            $this->lex();
            $this->eat(Token::TYPE_IDENTIFIER);

            if ($this->match(Token::TYPE_LPAREN)) {
                $this->variablesTypes = $this->parseVariableTypes();
            }
        }

        $this->lex();

        while (!$this->match(Token::TYPE_RBRACE) && !$this->end()) {
            if ($first) {
                $first = false;
            } else {
                $this->eatMulti([Token::TYPE_COMMA]);
            }

            if ($this->match(Token::TYPE_FRAGMENT_REFERENCE)) {
                $this->lex();

                if ($this->eat(Token::TYPE_ON)) {
                    $fields[] = $this->parseBodyItem(Token::TYPE_TYPED_FRAGMENT, $highLevel);
                } else {
                    $fields[] = $this->parseFragmentReference();
                }
            } else {
                $fields[] = $this->parseBodyItem($token, $highLevel);
            }
        }

        $this->expect(Token::TYPE_RBRACE);

        return $fields;
    }

    protected function checkVariableUsage()
    {
        if ($this->variablesTypes && count($this->variablesTypes) != count($this->variableTypeUsage)) {
            throw new UnusedVariableException(sprintf('Not all variables in query was used'));
        }
    }

    protected function parseVariableTypes()
    {
        $types = [];
        $first = true;

        $this->eat(Token::TYPE_LPAREN);

        while (!$this->match(Token::TYPE_RPAREN) && !$this->end()) {
            if ($first) {
                $first = false;
            } else {
                $this->expect(Token::TYPE_COMMA);
            }

            $this->eat(Token::TYPE_VARIABLE);
            $name = $this->parseIdentifier();
            $this->eat(Token::TYPE_COLON);
            $type = $this->parseIdentifier();

            if (array_key_exists($name, $types)) {
                throw new DuplicationVariableException(sprintf('"%s" variable duplication', $name));
            }

            $types[$name] = $type;
        }

        $this->expect(Token::TYPE_RPAREN);

        return $types;
    }

    protected function expect($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        throw $this->createUnexpected($this->peek());
    }

    protected function expectMulti($types)
    {
        if ($this->matchMulti($types)) {
            return $this->lex();
        }

        throw $this->createUnexpected($this->peek());
    }

    protected function parseReference()
    {
        $this->expectMulti([Token::TYPE_AMP, Token::TYPE_VARIABLE]);

        if ($this->match(Token::TYPE_NUMBER) || $this->match(Token::TYPE_IDENTIFIER)) {
            $name = $this->lex()->getData();

            if (!array_key_exists($name, $this->variablesTypes)) {
                throw new VariableTypeNotDefined(sprintf('Type for variable "%s" not defined', $name));
            }

            $this->variableTypeUsage[$name] = true;

            return new Variable($name, $this->variablesTypes[$name]);
        }

        throw $this->createUnexpected($this->peek());
    }

    protected function parseFragmentReference()
    {
        $name = $this->parseIdentifier();

        return new FragmentReference($name);
    }

    protected function parseIdentifier()
    {
        return $this->expectMulti([
            Token::TYPE_IDENTIFIER,
            Token::TYPE_MUTATION,
            Token::TYPE_QUERY,
            Token::TYPE_FRAGMENT,
        ])->getData();
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
            $fields = $this->parseBody($type == Token::TYPE_TYPED_FRAGMENT ? Token::TYPE_QUERY : $type, false);

            if ($type == Token::TYPE_QUERY) {
                return new Query($name, $alias, $arguments, $fields);
            } elseif ($type == Token::TYPE_TYPED_FRAGMENT) {
                return new TypedFragmentReference($name, $fields);
            } else {
                return new Mutation($name, $alias, $arguments, $fields);
            }
        } else {
            if ($highLevel && $type == Token::TYPE_MUTATION) {
                return new Mutation($name, $alias, $arguments);
            }

            return new Field($name, $alias, $arguments);
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
            case Token::TYPE_VARIABLE:
                return $this->parseReference();

            case Token::TYPE_NUMBER:
            case Token::TYPE_STRING:
            case Token::TYPE_IDENTIFIER:
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

            case Token::TYPE_IDENTIFIER:
                return $this->expect(Token::TYPE_IDENTIFIER)->getData();
        }

        throw new SyntaxErrorException('Can\'t parse argument');
    }

    protected function parseObject($createType = true)
    {
        $this->eat(Token::TYPE_LBRACE);

        $object = [];
        while (!$this->match(Token::TYPE_RBRACE) && !$this->end()) {
            $key = $this->expectMulti([Token::TYPE_STRING, Token::TYPE_IDENTIFIER])->getData();
            $this->expect(Token::TYPE_COLON);
            $value = $this->parseListValue();

            if ($this->peek()->getType() != Token::TYPE_RBRACE) {
                $this->expect(Token::TYPE_COMMA);
            }

            $object[$key] = $value;
        }

        $this->eat(Token::TYPE_RBRACE);

        return $createType ? new InputObject($object) : $object;
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

    protected function eat($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        return null;
    }

    protected function eatMulti($types)
    {
        if ($this->matchMulti($types)) {
            return $this->lex();
        }

        return null;
    }

    protected function matchMulti($types)
    {
        foreach ($types as $type) {
            if ($this->peek()->getType() == $type) {
                return true;
            }
        }

        return false;
    }

    protected function match($type)
    {
        return $this->peek()->getType() === $type;
    }
}
