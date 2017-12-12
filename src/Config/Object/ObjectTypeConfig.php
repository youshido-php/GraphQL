<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class ObjectTypeConfig
 *
 * @method setDescription(string $description)
 * @method string getDescription()
 */
class ObjectTypeConfig extends AbstractConfig
{
    use FieldsAwareConfigTrait;

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, 'final' => true],
            'interfaces'  => ['type' => TypeService::TYPE_ARRAY_OF_INTERFACES],
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
     * @return AbstractInterfaceType[]
     */
    public function getInterfaces()
    {
        return $this->get('interfaces', []);
    }
}
