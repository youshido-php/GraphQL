<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/19/16 9:00 AM
*/

namespace Youshido\GraphQL\Execution\Context;


use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

class ExecutionContext implements ExecutionContextInterface
{

    use ErrorContainerTrait;

    /** @var AbstractSchema */
    private $schema;

    /** @var Request */
    private $request;

    /** @var Container */
    private $container;

    public function __construct($container = null)
    {
        if (!$container) {
            $container = new Container();
        }
        $this->container = $container;
    }

    /**
     * @return AbstractSchema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param AbstractSchema $schema
     *
     * @return $this
     */
    public function setSchema(AbstractSchema $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
