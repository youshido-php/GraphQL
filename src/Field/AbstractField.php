<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractField implements FieldInterface
{

    use FieldsArgumentsAwareObjectTrait, AutoNameTrait;

    protected $isFinal = false;

    private $resolveCache = null;

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

    public function build(FieldConfig $config)
    {
    }

    public function setType($type)
    {
        $this->getConfig()->set('type', $type);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        if ($this->resolveCache === null) {
            $this->resolveCache = $this->getConfig()->getResolveFunction();

            if (!$this->resolveCache) {
                $this->resolveCache = false;
            }
        }
        if ($this->resolveCache) {
            $resolveFunction = $this->resolveCache;
            return $resolveFunction($value, $args, $info);
        } else {
            if (is_array($value) && array_key_exists($this->getName(), $value)) {
                return $value[$this->getName()];
            } elseif (is_object($value)) {
                return TypeService::getPropertyValue($value, $this->getName());
            } elseif ($this->getType()->getNamedType()->getKind() == TypeMap::KIND_SCALAR) {
                return null;
            } else {
                throw new \Exception(sprintf('Property "%s" not found in resolve result', $this->getName()));
            }
        }
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
