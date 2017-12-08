<?php

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;

use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

/**
 * Class Literal
 */
class Literal extends AbstractAst implements ValueInterface
{
    /** @var mixed */
    private $value;

    /**
     * @param mixed    $value
     * @param Location $location
     */
    public function __construct($value, Location $location)
    {
        parent::__construct($location);

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
