<?php

namespace Youshido\GraphQL\Parser;

use Youshido\GraphQL\Exception\Parser\SyntaxErrorException;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputList;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputObject;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Directive;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;

/**
 * Class Parser
 */
class Parser
{
    /** @var  Tokenizer */
    private $tokenizer;

    /** @var  ParseResult */
    private $parseResult;

    /**
     * @param string $source
     *
     * @return ParseResult
     * @throws SyntaxErrorException
     */
    public function parse($source = '')
    {
        $this->tokenizer   = new Tokenizer($source);
        $this->parseResult = new ParseResult();

        while (!$this->tokenizer->end()) {
            switch ($this->tokenizer->peek()->getType()) {
                case Token::TYPE_LBRACE:
                    foreach ($this->parseBody() as $query) {
                        $this->parseResult->addQuery($query);
                    }

                    break;
                case Token::TYPE_QUERY:
                    $queries = $this->parseOperation(Token::TYPE_QUERY);
                    foreach ($queries as $query) {
                        $this->parseResult->addQuery($query);
                    }

                    break;
                case Token::TYPE_MUTATION:
                    $mutations = $this->parseOperation(Token::TYPE_MUTATION);
                    foreach ($mutations as $mutation) {
                        $this->parseResult->addMutation($mutation);
                    }

                    break;

                case Token::TYPE_FRAGMENT:
                    $this->parseResult->addFragment($this->parseFragment());

                    break;

                default:
                    throw $this->tokenizer->createException('Incorrect request syntax');
            }
        }

        return $this->parseResult;
    }

    private function parseOperation($type = Token::TYPE_QUERY)
    {
        if ($this->tokenizer->eatMulti([Token::TYPE_QUERY, Token::TYPE_MUTATION])) {
            $this->tokenizer->eat(Token::TYPE_IDENTIFIER);

            if ($this->tokenizer->match(Token::TYPE_LPAREN)) {
                $this->parseVariables();
            }

            if ($this->tokenizer->match(Token::TYPE_AT)) {
                $this->parseResult->setDirectives($this->parseDirectiveList());
            }
        }

        $this->tokenizer->lex();

        $fields = [];
        while (!$this->tokenizer->match(Token::TYPE_RBRACE) && !$this->tokenizer->end()) {
            $this->tokenizer->eatMulti([Token::TYPE_COMMA]);

            $fields[] = $this->parseBodyItem($type, true);
        }

        $this->tokenizer->expect(Token::TYPE_RBRACE);

        return $fields;
    }

    private function parseBody($token = Token::TYPE_QUERY, $highLevel = true)
    {
        $fields = [];

        $this->tokenizer->lex();

        while (!$this->tokenizer->match(Token::TYPE_RBRACE) && !$this->tokenizer->end()) {
            $this->tokenizer->eatMulti([Token::TYPE_COMMA]);

            if ($this->tokenizer->match(Token::TYPE_FRAGMENT_REFERENCE)) {
                $this->tokenizer->lex();

                if ($this->tokenizer->eat(Token::TYPE_ON)) {
                    $fields[] = $this->parseBodyItem(Token::TYPE_TYPED_FRAGMENT, $highLevel);
                } else {
                    $fields[] = $this->parseFragmentReference();
                }
            } else {
                $fields[] = $this->parseBodyItem($token, $highLevel);
            }
        }

        $this->tokenizer->expect(Token::TYPE_RBRACE);

        return $fields;
    }

    private function parseVariables()
    {
        $this->tokenizer->eat(Token::TYPE_LPAREN);

        while (!$this->tokenizer->match(Token::TYPE_RPAREN) && !$this->tokenizer->end()) {
            $this->tokenizer->eat(Token::TYPE_COMMA);

            $variableToken = $this->tokenizer->eat(Token::TYPE_VARIABLE);
            $nameToken     = $this->eatIdentifierToken();
            $this->tokenizer->eat(Token::TYPE_COLON);

            $isArray              = false;
            $arrayElementNullable = true;

            if ($this->tokenizer->match(Token::TYPE_LSQUARE_BRACE)) {
                $isArray = true;

                $this->tokenizer->eat(Token::TYPE_LSQUARE_BRACE);
                $type = $this->eatIdentifierToken()->getData();

                if ($this->tokenizer->match(Token::TYPE_REQUIRED)) {
                    $arrayElementNullable = false;
                    $this->tokenizer->eat(Token::TYPE_REQUIRED);
                }

                $this->tokenizer->eat(Token::TYPE_RSQUARE_BRACE);
            } else {
                $type = $this->eatIdentifierToken()->getData();
            }

            $required = false;
            if ($this->tokenizer->match(Token::TYPE_REQUIRED)) {
                $required = true;
                $this->tokenizer->eat(Token::TYPE_REQUIRED);
            }

            $variable = new Variable(
                $nameToken->getData(),
                $type,
                $required,
                $isArray,
                $arrayElementNullable,
                $variableToken->getLocation()
            );

            if ($this->tokenizer->match(Token::TYPE_EQUAL)) {
                $this->tokenizer->eat(Token::TYPE_EQUAL);
                $variable->setDefaultValue($this->parseValue());
            }

            $this->parseResult->addVariable($variable);
        }

        $this->tokenizer->expect(Token::TYPE_RPAREN);
    }

    private function parseVariableReference()
    {
        $startToken = $this->tokenizer->expectMulti([Token::TYPE_VARIABLE]);

        if ($this->tokenizer->matchMulti([Token::TYPE_NUMBER, Token::TYPE_IDENTIFIER, Token::TYPE_QUERY])) {
            $name = $this->tokenizer->lex()->getData();

            if ($variable = $this->parseResult->getVariable($name)) {
                $variable->setUsed(true);
            }

            $variableReference = new VariableReference($name, $variable, $startToken->getLocation());
            $this->parseResult->addVariableReference($variableReference);

            return $variableReference;
        }

        throw $this->tokenizer->createUnexpectedTokenTypeException($this->tokenizer->peek()->getType());
    }

    private function parseFragmentReference()
    {
        $nameToken = $this->eatIdentifierToken();

        $directives = [];
        if ($this->tokenizer->match(Token::TYPE_AT)) {
            $directives = $this->parseDirectiveList();
        }

        $fragmentReference = new FragmentReference($nameToken->getData(), $nameToken->getLocation());
        $fragmentReference->setDirectives($directives);

        $this->parseResult->addFragmentReference($fragmentReference);

        return $fragmentReference;
    }

    private function eatIdentifierToken()
    {
        return $this->tokenizer->expectMulti([
            Token::TYPE_IDENTIFIER,
            Token::TYPE_MUTATION,
            Token::TYPE_QUERY,
            Token::TYPE_FRAGMENT,
        ]);
    }

    private function parseBodyItem($type = Token::TYPE_QUERY, $highLevel = true)
    {
        $nameToken = $this->eatIdentifierToken();
        $alias     = null;

        if ($this->tokenizer->eat(Token::TYPE_COLON)) {
            $alias     = $nameToken->getData();
            $nameToken = $this->eatIdentifierToken();
        }

        $arguments  = $this->tokenizer->match(Token::TYPE_LPAREN) ? $this->parseArgumentList() : [];
        $directives = $this->tokenizer->match(Token::TYPE_AT) ? $this->parseDirectiveList() : [];

        if ($this->tokenizer->match(Token::TYPE_LBRACE)) {
            $fields = $this->parseBody($type === Token::TYPE_TYPED_FRAGMENT ? Token::TYPE_QUERY : $type, false);

            if (!$fields) {
                throw $this->tokenizer->createUnexpectedTokenTypeException($this->tokenizer->peek()->getType());
            }

            if ($type === Token::TYPE_QUERY) {
                return new Query($nameToken->getData(), $alias, $arguments, $fields, $directives, $nameToken->getLocation());
            }
            if ($type === Token::TYPE_TYPED_FRAGMENT) {
                return new TypedFragmentReference($nameToken->getData(), $fields, $directives, $nameToken->getLocation());
            }

            return new Mutation($nameToken->getData(), $alias, $arguments, $fields, $directives, $nameToken->getLocation());
        }

        if ($highLevel && $type === Token::TYPE_MUTATION) {
            return new Mutation($nameToken->getData(), $alias, $arguments, [], $directives, $nameToken->getLocation());
        }
        if ($highLevel && $type === Token::TYPE_QUERY) {
            return new Query($nameToken->getData(), $alias, $arguments, [], $directives, $nameToken->getLocation());
        }

        return new Field($nameToken->getData(), $alias, $arguments, $directives, $nameToken->getLocation());
    }

    private function parseArgumentList()
    {
        $args = [];

        $this->tokenizer->expect(Token::TYPE_LPAREN);
        while (!$this->tokenizer->match(Token::TYPE_RPAREN) && !$this->tokenizer->end()) {
            $this->tokenizer->eat(Token::TYPE_COMMA);
            $args[] = $this->parseArgument();
        }

        $this->tokenizer->expect(Token::TYPE_RPAREN);

        return $args;
    }

    private function parseArgument()
    {
        $nameToken = $this->eatIdentifierToken();
        $this->tokenizer->expect(Token::TYPE_COLON);
        $value = $this->parseValue();

        return new Argument($nameToken->getData(), $value, $nameToken->getLocation());
    }

    private function parseDirectiveList()
    {
        $directives = [];

        while ($this->tokenizer->match(Token::TYPE_AT)) {
            $directives[] = $this->parseDirective();
            $this->tokenizer->eat(Token::TYPE_COMMA);
        }

        return $directives;
    }

    private function parseDirective()
    {
        $this->tokenizer->expect(Token::TYPE_AT);

        $nameToken = $this->eatIdentifierToken();
        $args      = $this->tokenizer->match(Token::TYPE_LPAREN) ? $this->parseArgumentList() : [];

        return new Directive($nameToken->getData(), $args, $nameToken->getLocation());
    }

    private function parseValue()
    {
        switch ($this->tokenizer->peek()->getType()) {
            case Token::TYPE_LSQUARE_BRACE:
                return $this->parseList();

            case Token::TYPE_LBRACE:
                return $this->parseObject();

            case Token::TYPE_VARIABLE:
                return $this->parseVariableReference();

            case Token::TYPE_NUMBER:
            case Token::TYPE_STRING:
            case Token::TYPE_IDENTIFIER:
            case Token::TYPE_NULL:
            case Token::TYPE_TRUE:
            case Token::TYPE_FALSE:
                $token = $this->tokenizer->lex();

                return new Literal($token->getData(), $token->getLocation());
        }

        throw $this->tokenizer->createUnexpectedTokenTypeException($this->tokenizer->peek()->getType());
    }

    private function parseList($createType = true)
    {
        $startToken = $this->tokenizer->eat(Token::TYPE_LSQUARE_BRACE);

        $list = [];
        while (!$this->tokenizer->match(Token::TYPE_RSQUARE_BRACE) && !$this->tokenizer->end()) {
            $list[] = $this->parseListValue();

            $this->tokenizer->eat(Token::TYPE_COMMA);
        }

        $this->tokenizer->expect(Token::TYPE_RSQUARE_BRACE);

        return $createType ? new InputList($list, $startToken->getLocation()) : $list;
    }

    private function parseListValue()
    {
        switch ($this->tokenizer->peek()->getType()) {
            case Token::TYPE_NUMBER:
            case Token::TYPE_STRING:
            case Token::TYPE_TRUE:
            case Token::TYPE_FALSE:
            case Token::TYPE_NULL:
            case Token::TYPE_IDENTIFIER:
                return $this->tokenizer->expect($this->tokenizer->peek()->getType())->getData();

            case Token::TYPE_VARIABLE:
                return $this->parseVariableReference();

            case Token::TYPE_LBRACE:
                return $this->parseObject(true);

            case Token::TYPE_LSQUARE_BRACE:
                return $this->parseList(false);
        }

        throw $this->tokenizer->createException('Can\'t parse argument');
    }

    private function parseObject($createType = true)
    {
        $startToken = $this->tokenizer->eat(Token::TYPE_LBRACE);

        $object = [];
        while (!$this->tokenizer->match(Token::TYPE_RBRACE) && !$this->tokenizer->end()) {
            $key = $this->tokenizer->expectMulti([Token::TYPE_STRING, Token::TYPE_IDENTIFIER])->getData();
            $this->tokenizer->expect(Token::TYPE_COLON);
            $value = $this->parseListValue();

            $this->tokenizer->eat(Token::TYPE_COMMA);

            $object[$key] = $value;
        }

        $this->tokenizer->eat(Token::TYPE_RBRACE);

        return $createType ? new InputObject($object, $startToken->getLocation()) : $object;
    }

    private function parseFragment()
    {
        $this->tokenizer->lex();
        $nameToken = $this->eatIdentifierToken();

        $this->tokenizer->eat(Token::TYPE_ON);

        $model = $this->eatIdentifierToken();

        $directives = $this->tokenizer->match(Token::TYPE_AT) ? $this->parseDirectiveList() : [];

        $fields = $this->parseBody(Token::TYPE_QUERY, false);

        return new Fragment($nameToken->getData(), $model->getData(), $directives, $fields, $nameToken->getLocation());
    }
}
