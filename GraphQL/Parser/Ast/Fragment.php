<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


class Fragment
{

    protected $name;

    protected $model;

    /** @var Field[]|Query[] */
    protected $children;

    /** @var bool */
    private $used = false;

    /**
     * Fragment constructor.
     *
     * @param                 $name
     * @param                 $model
     * @param Field[]|Query[] $children
     */
    public function __construct($name, $model, $children)
    {
        $this->name     = $name;
        $this->model    = $model;
        $this->children = $children;
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @param boolean $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}