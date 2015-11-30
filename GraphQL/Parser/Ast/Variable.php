<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;

class Variable
{

    private $name;

    private $value;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getValue()
    {
        if (!$this->value) {
            throw new \Exception('Value not setted to variable');
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}