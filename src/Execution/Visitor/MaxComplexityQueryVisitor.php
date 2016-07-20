<?php

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
   * @return mixed
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