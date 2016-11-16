<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;


use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class InputObject extends AbstractAst implements ValueInterface
{

    protected $object = [];

    /**
     * @param array    $object
     * @param Location $location
     */
    public function __construct(array $object, Location $location)
    {
        parent::__construct($location);

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