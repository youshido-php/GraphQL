<?php
/**
 * Date: 04.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition\Traits;


use Youshido\GraphQL\Schema;

trait SchemaContainableTrait
{

    /** @var  Schema */
    protected $schema;

    /**
     * @param $schema Schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }
}