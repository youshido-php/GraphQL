<?php

namespace Youshido\GraphQL\Execution\Visitor;


use Youshido\GraphQL\Field\AbstractField;

abstract class AbstractQueryVisitor {

  protected $initialValue = 0;

  protected $memo;

  public function __construct() {
    $this->memo = $this->initialValue;
  }

  public function getMemo() {
    return $this->memo;
  }

  abstract public function visit(AbstractField $field);
}