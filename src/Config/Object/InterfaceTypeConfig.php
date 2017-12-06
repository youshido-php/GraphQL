<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class InterfaceTypeConfig
 *
 * @method $this setDescription(string $description)
 */
class InterfaceTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait, ArgumentsAwareConfigTrait;

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'resolveType' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true],
        ];
    }

    /**
     * Configure class properties
     */
    protected function build()
    {
        $this->buildFields();
    }

    /**
     * @param mixed $object
     *
     * @return mixed
     * @throws ConfigurationException
     */
    public function resolveType($object)
    {
        $callable = $this->get('resolveType');

        if ($callable && is_callable($callable)) {
            return $callable($object);
        }
        if (is_callable([$this->contextObject, 'resolveType'])) {
            return $this->contextObject->resolveType($object);
        }

        throw new ConfigurationException('There is no valid resolveType for ' . $this->getName());
    }
}
