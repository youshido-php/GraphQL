<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


use Youshido\GraphQL\Parser\Location;

class Fragment extends AbstractAst
{

    use AstDirectivesTrait;

    protected $name;

    protected $model;

    /** @var Field[]|Query[] */
    protected $fields;

    /** @var bool */
    private $used = false;

    /**
     * @param string          $name
     * @param string          $model
     * @param array           $directives
     * @param Field[]|Query[] $fields
     * @param Location        $location
     */
    public function __construct($name, $model, array $directives, array $fields, Location $location)
    {
        parent::__construct($location);

        $this->name   = $name;
        $this->model  = $model;
        $this->fields = $fields;
        $this->setDirectives($directives);
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

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return Field[]|Query[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Field[]|Query[] $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }
}