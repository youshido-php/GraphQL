<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;


use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

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

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return $this->config->get('type');
    }

    public function getKind()
    {
        return $this->getType()->getKind();
    }

    public function resolve($value, $args, $type)
    {
        $resolveFn = $this->config->get('resolve', null);

        return $resolveFn ? $resolveFn($value, $args, $type) : null;
    }
}
