<?php

namespace Youshido\GraphQL\Execution\Visitor;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Exception\GraphQLException;

/**
 * Class MaxComplexityQueryVisitor
 */
class MaxComplexityQueryVisitor extends AbstractQueryVisitor
{
    /**
     * @var int max score allowed before throwing an exception (causing processing to stop)
     */
    public $maxScore;

    /**
     * @var int default score for nodes without explicit cost functions
     */
    protected $defaultScore = 1;

    /**
     * MaxComplexityQueryVisitor constructor.
     *
     * @param int $max max allowed complexity score
     */
    public function __construct($max)
    {
        parent::__construct();

        $this->maxScore = $max;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(array $args, FieldConfig $fieldConfig, $childScore = 0)
    {
        $cost = $fieldConfig->getCost();
        if (is_callable($cost)) {
            $cost = $cost($args, $fieldConfig, $childScore);
        }

        $cost       = null === $cost ? $this->defaultScore : $cost;
        $this->memo += $cost;

        if ($this->memo > $this->maxScore) {
            throw new GraphQLException(sprintf('Query exceeded max allowed complexity of %s', $this->maxScore));
        }

        return $cost;
    }
}
