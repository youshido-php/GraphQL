<?php
/**
 * Date: 10/24/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;


use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class VariableReference extends AbstractAst implements ValueInterface
{

    /** @var  string */
    private $name;

    /** @var  Variable */
    private $variable;

    /** @var  mixed */
    private $value;

    /**
     * @param string        $name
     * @param Variable|null $variable
     * @param Location      $location
     */
    public function __construct($name, Variable $variable = null, Location $location)
    {
        parent::__construct($location);

        $this->name     = $name;
        $this->variable = $variable;
    }

    public function getVariable()
    {
        return $this->variable;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
