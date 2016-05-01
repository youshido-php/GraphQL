<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Field;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\Config\Traits\ConfigCallTrait;

/**
 * Class Field
 * @package Youshido\GraphQL\Type\Field
 *
 */
class Field
{
    use ConfigCallTrait;

    /** @var FieldConfig */
    protected $config;

    public function __construct($config)
    {
        $this->config = new FieldConfig($config);
    }

    /**
     * @return FieldConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}