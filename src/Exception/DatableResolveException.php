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
 * Date: 22.12.15.
 */

namespace Youshido\GraphQL\Exception;

use Youshido\GraphQL\Exception\Interfaces\DatableExceptionInterface;

class DatableResolveException extends \Exception implements DatableExceptionInterface
{
    /** @var array */
    protected $data;

    public function __construct($message, $code = 0, $data = [])
    {
        parent::__construct($message, $code);

        $this->setData($data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}
