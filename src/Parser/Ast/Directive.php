<?php

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Location;

/**
 * Class Directive
 */
class Directive extends AbstractAst
{
    use AstArgumentsTrait;

    /** @var string */
    private $name;

    /**
     * @param string   $name
     * @param array    $arguments
     * @param Location $location
     */
    public function __construct($name, array $arguments, Location $location)
    {
        parent::__construct($location);

        $this->name = $name;
        $this->setArguments($arguments);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
