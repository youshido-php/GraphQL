<?php
/*
* Concrete implementation of query visitor.
*
* Enforces maximum complexity on a query, computed from "cost" functions on
* the fields touched by that query.
*
* @author Ben Roberts <bjr.roberts@gmail.com>
* created: 7/11/16 11:05 AM
*/

namespace Youshido\GraphQL\Execution\Visitor;


use Youshido\GraphQL\Config\Field\FieldConfig;

class MaxComplexityQueryVisitor extends AbstractQueryVisitor {

  public $maxScore;

  protected $defaultScore = 1;

  public function __construct($max) {
    parent::__construct();

    $this->maxScore = $max;
  }

  /**
   * @param array       $args
   * @param FieldConfig $fieldConfig
   * @param int         $childScore
   *
   * @return int|null
   * @throws \Exception
   */
  public function visit(array $args, FieldConfig $fieldConfig, $childScore = 0) {
    $cost = $fieldConfig->get('cost');
    if (is_callable($cost)) {
      $cost = $cost($args, $fieldConfig, $childScore);
    }
    $cost = $cost ?: $this->defaultScore;
    $this->memo += $cost;
    if ($this->memo > $this->maxScore) {
      throw new \Exception('query exceeded max allowed complexity of ' . $this->maxScore);
    }
    return $cost;
  }
}