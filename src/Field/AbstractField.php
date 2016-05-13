<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractField
{

    use ConfigCallTrait, AutoNameTrait;

    /** @var FieldConfig */
    protected $config;

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
    }

    /**
     * @return AbstractObjectType
     */
    abstract public function getType();

    public function resolve($value, $args = [], $type = null)
    {
        if ($resolveFunc = $this->getConfig()->getResolveFunction()) {
            return $resolveFunc($value, $args, $this->getType());
        }

        return null;
    }
}