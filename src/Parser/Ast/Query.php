<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 23.11.15.
 */

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\FragmentInterface;
use Youshido\GraphQL\Parser\Location;

class Query extends AbstractAst implements FieldInterface
{
    use AstArgumentsTrait;
    use AstDirectivesTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $alias;

    /** @var Field[]|Query[] */
    protected $fields = [];

    /**
     * Query constructor.
     *
     * @param string   $name
     * @param string   $alias
     * @param array    $arguments
     * @param array    $fields
     * @param array    $directives
     * @param Location $location
     */
    public function __construct($name, $alias, array $arguments, array $fields, array $directives, Location $location)
    {
        parent::__construct($location);

        $this->name  = $name;
        $this->alias = $alias;
        $this->setFields($fields);
        $this->setArguments($arguments);
        $this->setDirectives($directives);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Field[]|FragmentInterface[]|Query[]
     */
    public function getFields()
    {
        return \array_values($this->fields);
    }

    /**
     * @return bool
     */
    public function hasFields()
    {
        return (bool) \count($this->fields);
    }

    /**
     * @param Field[]|Query[] $fields
     */
    public function setFields($fields): void
    {
        /*
         * we cannot store fields by name because of TypedFragments
         */
        $this->fields = $fields;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function hasField($name, $deep = false)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() === $name) {
                return true;
            }

            if ($deep && $field instanceof self) {
                if ($field->hasField($name)) {
                    return true;
                }
            }
        }

        return false;
    }
}
