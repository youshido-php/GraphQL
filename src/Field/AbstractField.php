<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractField
{

    use ConfigCallTrait;

    /** @var FieldConfig */
    protected $config;

    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
        }

        if (empty($config['name'])) {
            $config['name'] = $this->getName();
        }

        if (TypeService::isScalarTypeName($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }

        $this->config = new FieldConfig($config, $this);
    }

    /**
     * @return FieldConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    protected function getConfigValue($key, $defaultValue = null)
    {
        return !empty($this->config) ? $this->config->get($key, $defaultValue) : $defaultValue;
    }

    /**
     * @return AbstractObjectType
     */
    abstract public function getType();

    /**
     * @return string
     */
    abstract public function getName();

    public function resolve($value, $args = [], $type = null)
    {
        if ($resolveFunc = $this->getConfig()->getResolveFunction()) {
            return $resolveFunc($value, $args, $this->getType());
        }

        return null;
    }
}