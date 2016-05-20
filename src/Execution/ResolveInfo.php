<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/19/16 8:58 AM
*/

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Type\AbstractType;

class ResolveInfo
{
    /** @var  AbstractField */
    protected $field;

    /** @var Field[] */
    protected $fieldASTList;

    /** @var  AbstractType */
    protected $returnType;

    /** @var ExecutionContext */
    protected $executionContext;

    public function __construct(AbstractField $field, array $fieldASTList, AbstractType $returnType, ExecutionContext $executionContext)
    {
        $this->field            = $field;
        $this->fieldASTList     = $fieldASTList;
        $this->returnType       = $returnType;
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
     * @return AbstractField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return Field[]
     */
    public function getFieldASTList()
    {
        return $this->fieldASTList;
    }

    /**
     * @return AbstractType
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @param mixed $returnType
     *
     * @return ResolveInfo
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;

        return $this;
    }


}
