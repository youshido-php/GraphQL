<?php
/**
 * Date: 5/20/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Execution\Context;


use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;

interface ExecutionContextInterface extends ErrorContainerInterface
{

    /**
     * @return AbstractSchema
     */
    public function getSchema();

    /**
     * @param AbstractSchema $schema
     *
     * @return $this
     */
    public function setSchema(AbstractSchema $schema);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request);

    public function getContainer();

    public function setContainer($container);

}
