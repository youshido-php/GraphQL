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
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11/27/15 2:32 AM
 */

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class ObjectTypeConfig.
 *
 * @method setDescription(string $description)
 * @method string getDescription()
 */
class ObjectTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait;

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
     * @return AbstractInterfaceType[]
     */
    public function getInterfaces()
    {
        return $this->get('interfaces', []);
    }

    protected function build(): void
    {
        $this->buildFields();
    }
}
