<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Exception\ResolveException;
use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class AbstractField
 */
abstract class AbstractField implements FieldInterface
{
    use FieldsArgumentsAwareObjectTrait, AutoNameTrait {
        getName as getAutoName;
    }

    /** @var bool */
    protected $isFinal = false;

    /** @var bool|string */
    private $nameCache;

    /**
     * AbstractField constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
            $config['name'] = $this->getName();

            if (empty($config['name'])) {
                $config['name'] = $this->getAutoName();
            }
        }

        if (TypeService::isScalarType($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }
        $this->nameCache = isset($config['name']) ? $config['name'] : $this->getAutoName();

        $this->config = new FieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    /**
     * @return AbstractType|AbstractObjectType
     */
    abstract public function getType();

    /**
     * @param FieldConfig $config
     */
    public function build(FieldConfig $config)
    {
    }

    /**
     * @param AbstractType|AbstractObjectType $type
     */
    public function setType($type)
    {
        $this->getConfig()->set('type', $type);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->nameCache;
    }

    /**
     * @param mixed                $value
     * @param array                $args
     * @param ResolveInfoInterface $info
     *
     * @return mixed|null
     * @throws ResolveException
     */
    public function resolve($value, array $args, ResolveInfoInterface $info)
    {
        if ($resolveFunction = $this->getConfig()->getResolveFunction()) {
            return $resolveFunction($value, $args, $info);
        }

        if (is_array($value) && array_key_exists($this->getName(), $value)) {
            return $value[$this->getName()];
        }

        if (is_object($value) && $info->getExecutionContext()->getPropertyAccessor()) {
            return $info->getExecutionContext()->getPropertyAccessor()->getValue($value, $this->getName());
        }

        if ($this->getType()->getNamedType()->getKind() === TypeMap::KIND_SCALAR) {
            return null;
        }

        throw new ResolveException(sprintf('Property "%s" not found in resolve result', $this->getName()));
    }

    /**
     * @return callable|null
     */
    public function getResolveFunction()
    {
        return $this->getConfig()->getResolveFunction();
    }

    /**
     * @return bool
     */
    public function isDeprecated()
    {
        return $this->getConfigValue('isDeprecated', false);
    }

    /**
     * @return string
     */
    public function getDeprecationReason()
    {
        return $this->getConfigValue('deprecationReason');
    }
}
