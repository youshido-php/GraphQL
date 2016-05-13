<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;


use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Config\Traits\ConfigCallTrait;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractInputField
{

    use ConfigCallTrait, AutoNameTrait;

    /** @var InputFieldConfig */
    protected $config;

    protected $isFinal = false;

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

        $this->config = new InputFieldConfig($config, $this, $this->isFinal);
    }

    /**
     * @return AbstractInputObjectType
     */
    abstract public function getType();

    public function getDefaultValue()
    {
        return $this->config->getDefaultValue();
    }

    //todo: think about serialize, parseValue methods

}