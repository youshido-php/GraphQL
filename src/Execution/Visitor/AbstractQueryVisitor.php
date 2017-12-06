<?php

namespace Youshido\GraphQL\Execution\Visitor;

use Youshido\GraphQL\Config\Field\FieldConfig;

/**
 * Class AbstractQueryVisitor
 */
abstract class AbstractQueryVisitor
{
    /**
     * @var int initial value of $this->memo
     */
    protected $initialValue = 0;

    /**
     * @var mixed the accumulator
     */
    protected $memo;

    /**
     * AbstractQueryVisitor constructor.
     */
    public function __construct()
    {
        $this->memo = $this->initialValue;
    }

    /**
     * @return mixed getter for the memo, in case callers want to inspect it after a process run
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Visit a query node.  See class docstring.
     *
     * @param array       $args
     * @param FieldConfig $fieldConfig
     * @param int         $childScore
     *
     * @return int|null
     */
    abstract public function visit(array $args, FieldConfig $fieldConfig, $childScore = 0);
}
