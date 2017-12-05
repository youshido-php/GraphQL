<?php

namespace Youshido\GraphQL\Parser;

/**
 * Class Location
 */
class Location
{
    /** @var  int */
    private $line;

    /** @var  int */
    private $column;

    /**
     * Location constructor.
     *
     * @param int $line
     * @param int $column
     */
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

    /**
     * @param int $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @param int $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'line'   => $this->getLine(),
            'column' => $this->getColumn(),
        ];
    }
}
