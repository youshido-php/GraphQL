<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 13.05.16.
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Type\InputTypeInterface;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractInputField implements InputFieldInterface
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

        $this->config = new InputFieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    public function build(InputFieldConfig $config): void
    {
    }

    /**
     * @return InputTypeInterface
     */
    abstract public function getType();

    public function getDefaultValue()
    {
        return $this->config->getDefaultValue();
    }

    //todo: think about serialize, parseValue methods
}
