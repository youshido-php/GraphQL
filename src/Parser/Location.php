<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 16.11.16.
 */

namespace Youshido\GraphQL\Parser;

class Location
{
    /** @var int */
    private $line;

    /** @var int */
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
            'column' => $this->getColumn(),
        ];
    }
}
