<?php
/**
 * Date: 22.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Exception;

use Youshido\GraphQL\Exception\Interfaces\DatableExceptionInterface;

class DatableResolveException extends \Exception implements DatableExceptionInterface
{

    /** @var  array */
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
    public function setData($data)
    {
        $this->data = $data;
    }

}