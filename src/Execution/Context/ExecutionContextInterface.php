<?php

namespace Youshido\GraphQL\Execution\Context;

use Psr\Container\ContainerInterface;
use Youshido\GraphQL\Execution\PropertyAccessor\PropertyAccessorInterface;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;

/**
 * Interface ExecutionContextInterface
 */
interface ExecutionContextInterface extends ErrorContainerInterface
{
    /**
     * @return AbstractSchema
     */
    public function getSchema();

    /**
     * @param AbstractSchema $schema
     */
    public function setSchema(AbstractSchema $schema);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);

    /**
     * @return PropertyAccessorInterface
     */
    public function getPropertyAccessor();

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor);

    /**
     * @param \Exception $exception
     */
    public function handleException(\Exception $exception);

    /**
     * @return array
     */
    public function getErrorsData();
}
