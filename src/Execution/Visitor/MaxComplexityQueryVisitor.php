<?php

namespace Youshido\GraphQL\Execution\Visitor;


use Youshido\GraphQL\Field\AbstractField;

class MaxComplexityQueryVisitor extends AbstractQueryVisitor {

  public $maxScore;

  protected $defaultScore = 1;

  public function __construct($max) {
    parent::__construct();

    $this->maxScore = $max;
  }

  public function visit(AbstractField $field) {
    $cost = $field->getConfig()->get('cost');
    if (is_callable($cost)) {
      $cost = $cost();
    }
    $this->memo += $cost ?: $this->defaultScore;
    if ($this->memo > $this->maxScore) {
      throw new \Exception('query exceeded max allowed complexity of ' . $this->maxScore);
    }
    return $this->memo;
  }
}