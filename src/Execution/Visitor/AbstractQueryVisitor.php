<?php
/*
* Abstract query visitor.
*
* @author Ben Roberts <bjr.roberts@gmail.com>
* created: 7/11/16 11:03 AM
*/

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
   * @return int|null
   */
  abstract public function visit(array $args, FieldConfig $fieldConfig, $childScore = 0);
}