<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/19/16 9:00 AM
*/

namespace Youshido\GraphQL\Execution;


use Youshido\GraphQL\Schema\AbstractSchema;

class ExecutionContext
{
    private $schema;

    private $request;

    public function __construct(AbstractSchema $schema)
    {
        $this->schema = $schema;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     * @return ExecutionContext
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}
