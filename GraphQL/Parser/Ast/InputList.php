<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


class InputList
{

    protected $list = [];

    /**
     * InputList constructor.
     *
     * @param array $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }


}