<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractField
{

    use FieldsArgumentsAwareObjectTrait, AutoNameTrait;

    protected $isFinal = false;

    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
            $config['name'] = $this->getName();
        }

        if (TypeService::isScalarType($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }

        $this->config = new FieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    /**
     * @return AbstractObjectType
     */
    abstract public function getType();

    public function build(FieldConfig $config) {

    }

    public function setType($type)
    {
        $this->getConfig()->set('type', $type);
    }

    public function resolve($value, $args = [], $type = null)
    {
        if ($resolveFunc = $this->getConfig()->getResolveFunction()) {
            return $resolveFunc($value, $args, $this->getType());
        }

        return null;
    }

    public function isDeprecated()
    {
        return $this->getConfigValue('isDeprecated');
    }

    public function getDeprecationReason()
    {
        return $this->getConfigValue('deprecationReason');
    }
}
