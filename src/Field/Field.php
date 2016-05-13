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
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

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
        $type         = $this->config->get('type');

        if (!is_object($type)) {
            if (TypeService::isScalarType($type)) {
                $this->config->set('type', TypeFactory::getScalarType($type));
            } else {
                throw new ConfigurationException('Invalid type for the field ' . $this->config->getName());
            }
        }
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
        return $this->getConfig()->get('type');
    }

    public function getName()
    {
        return $this->getConfig()->get('name');
    }

    public function getKind()
    {
        return $this->getType()->getKind();
    }

    public function resolve($value, $args = [], $type = null)
    {
        if ($this->config->issetResolve()) {
            $resolveFunc = $this->config->getResolveFunction();

            return $resolveFunc($value, $args, $this->getType());
        }

        return null;
    }
}
