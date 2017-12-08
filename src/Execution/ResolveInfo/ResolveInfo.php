<?php

namespace Youshido\GraphQL\Execution\ResolveInfo;

use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Type\AbstractType;

/**
 * Class ResolveInfo
 */
class ResolveInfo implements ResolveInfoInterface
{
    /** @var  FieldInterface */
    protected $field;

    /** @var Field[] */
    protected $fieldASTList;

    /** @var ExecutionContextInterface */
    protected $executionContext;

    /**
     * This property is to be used for DI in various scenario
     * Added to original class to keep backward compatibility
     * because of the way AbstractField::resolve has been declared
     *
     * @var mixed $container
     */
    protected $container;

    /**
     * ResolveInfo constructor.
     *
     * @param FieldInterface            $field
     * @param array                     $fieldASTList
     * @param ExecutionContextInterface $executionContext
     */
    public function __construct(FieldInterface $field, array $fieldASTList, ExecutionContextInterface $executionContext)
    {
        $this->field            = $field;
        $this->fieldASTList     = $fieldASTList;
        $this->executionContext = $executionContext;
    }

    /**
     * @return ExecutionContextInterface
     */
    public function getExecutionContext()
    {
        return $this->executionContext;
    }

    /**
     * @return FieldInterface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $fieldName
     *
     * @return null|Query|Field
     */
    public function getFieldAST($fieldName)
    {
        $field = null;
        foreach ($this->getFieldASTList() as $fieldAST) {
            if ($fieldAST->getName() === $fieldName) {
                $field = $fieldAST;
                break;
            }
        }

        return $field;
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
        return $this->field->getType();
    }

    /**
     * @return Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->executionContext->getContainer();
    }
}
