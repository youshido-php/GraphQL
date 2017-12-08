<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2/27/17 8:45 AM
 */

namespace Youshido\GraphQL\Execution\ResolveInfo;

use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Type\AbstractType;

interface ResolveInfoInterface
{
    /**
     * @return ExecutionContextInterface
     */
    public function getExecutionContext();

    /**
     * @return FieldInterface
     */
    public function getField();

    /**
     * @param string $fieldName
     *
     * @return null|Query|Field
     */
    public function getFieldAST($fieldName);

    /**
     * @return Field[]
     */
    public function getFieldASTList();

    /**
     * @return AbstractType
     */
    public function getReturnType();

    public function getContainer();
}
