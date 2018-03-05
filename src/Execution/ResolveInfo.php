<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5/19/16 8:58 AM
 */

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Type\AbstractType;

class ResolveInfo
{
    /** @var FieldInterface */
    protected $field;

    /** @var Field[] */
    protected $fieldASTList;

    /** @var ExecutionContextInterface */
    protected $executionContext;

    /**
     * This property is to be used for DI in various scenario
     * Added to original class to keep backward compatibility
     * because of the way AbstractField::resolve has been declared.
     *
     * @var mixed
     */
    protected $container;

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
     * @return Field|Query|null
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

    public function getContainer()
    {
        return $this->executionContext->getContainer();
    }
}
