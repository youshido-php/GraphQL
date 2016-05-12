<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser;

class Token
{

    const TYPE_END = 'end';
    const TYPE_IDENTIFIER = 'identifier';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_ON = 'on';

    const TYPE_QUERY = 'query';
    const TYPE_MUTATION = 'mutation';
    const TYPE_FRAGMENT = 'fragment';
    const TYPE_FRAGMENT_REFERENCE = '...';
    const TYPE_TYPED_FRAGMENT = 'typed fragment';

    const TYPE_LT = '<';
    const TYPE_GT = '>';
    const TYPE_LBRACE = '{';
    const TYPE_RBRACE = '}';
    const TYPE_LPAREN = '(';
    const TYPE_RPAREN = ')';
    const TYPE_LSQUARE_BRACE = '[';
    const TYPE_RSQUARE_BRACE = ']';
    const TYPE_COLON = ':';
    const TYPE_COMMA = ',';
    const TYPE_AMP = '&';
    const TYPE_VARIABLE = '$';
    const TYPE_POINT = '.';

    const TYPE_NULL = 'null';
    const TYPE_TRUE = 'true';
    const TYPE_FALSE = 'false';


    /** @var mixed */
    private $data;
    /** @var  string */
    private $type;

    public function __construct($type, $data = null)
    {
        $this->type = $type;
        $this->data = $data;

        if ($this->getType() == self::TYPE_TRUE) {
            $this->data = true;
        }

        if ($this->getType() == self::TYPE_FALSE) {
            $this->data = false;
        }

        if ($this->getType() == self::TYPE_NULL) {
            $this->data = null;
        }

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

}
