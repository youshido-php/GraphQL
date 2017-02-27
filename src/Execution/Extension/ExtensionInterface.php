<?php

namespace Youshido\GraphQL\Execution\Extension;
use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;


/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2/27/17 8:30 AM
 */
interface ExtensionInterface
{

    /**
     * @param ExecutionContextInterface $executionContext
     * @param arra y                    $result
     * @return array
     */
    public function processRequest(ExecutionContextInterface $executionContext, $result);

    /**
     * @return string
     */
    public function getName();

}