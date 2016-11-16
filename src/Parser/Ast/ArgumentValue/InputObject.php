<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;


use Youshido\GraphQL\Parser\Ast\AbstractAst;

class InputObject extends AbstractAst implements ValueInterface
{

    protected $object = [];

    /**
     * InputList constructor.
     *
     * @param array $object
     */
    public function __construct(array $object)
    {
        $this->object = $object;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->object;
    }

    /**
     * @param array $value
     */
    public function setValue($value)
    {
        $this->object = $value;
    }

}