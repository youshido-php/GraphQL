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

    /** @var  Request */
    private $request;

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
}
