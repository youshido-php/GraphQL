<?php
/**
 * Date: 16.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser;


class Location
{

    /** @var  integer */
    private $line;

    /** @var  integer */
    private $column;

    public function __construct($line, $column)
    {
        $this->line   = $line;
        $this->column = $column;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }


    public function toArray()
    {
        return [
            'line'   => $this->getLine(),
            'column' => $this->getColumn()
        ];
    }

}