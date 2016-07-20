<?php

namespace Youshido\GraphQL\Execution\Visitor;


use Youshido\GraphQL\Config\Field\FieldConfig;

abstract class AbstractQueryVisitor {

  protected $initialValue = 0;

  protected $memo;

  public function __construct() {
    $this->memo = $this->initialValue;
  }

  public function getMemo() {
    return $this->memo;
  }

  /**
   * @param array       $args
   * @param FieldConfig $fieldConfig
   * @param int         $childScore
   *
   * @return mixed
   */
  abstract public function visit(array $args, FieldConfig $fieldConfig, $childScore = 0);
}