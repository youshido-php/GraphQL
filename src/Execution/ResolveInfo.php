<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/19/16 8:58 AM
*/

namespace Youshido\GraphQL\Execution;

class ResolveInfo
{
    protected $field;

    protected $fieldASTList;

    protected $returnType;

//    protected $parentType;

    /** @var ExecutionContext */
    protected $executionContext;

    public function __construct($field, $fieldASTList, $returnType, $executionContext)
    {
        $this->field = $field;
        $this->fieldASTList = $fieldASTList;
        $this->returnType = $returnType;
        $this->executionContext = $executionContext;
    }

    /**
     * @return ExecutionContext
     */
    public function getExecutionContext()
    {
        return $this->executionContext;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getFieldASTList()
    {
        return $this->fieldASTList;
    }

    /**
     * @return mixed
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @param mixed $returnType
     * @return ResolveInfo
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;

        return $this;
    }


}
