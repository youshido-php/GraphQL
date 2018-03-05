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
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 12/5/15 12:18 AM
 */

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class InterfaceTypeConfig.
 *
 * @method $this setDescription(string $description)
 */
class InterfaceTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait, ArgumentsAwareConfigTrait;

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'resolveType' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true],
        ];
    }

    public function resolveType($object)
    {
        $callable = $this->get('resolveType');

        if ($callable && \is_callable($callable)) {
            return \call_user_func_array($callable, [$object]);
        }

        if (\is_callable([$this->contextObject, 'resolveType'])) {
            return $this->contextObject->resolveType($object);
        }

        throw new ConfigurationException('There is no valid resolveType for ' . $this->getName());
    }

    protected function build(): void
    {
        $this->buildFields();
    }
}
