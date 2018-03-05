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
 * created: 12/1/15 11:28 PM
 */

namespace Youshido\GraphQL\Config\Field;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class InputFieldConfig.
 *
 * @method $this setDescription(string $description)
 */
class InputFieldConfig extends AbstractConfig
{
    public function getRules()
    {
        return [
            'name'              => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'type'              => ['type' => TypeService::TYPE_ANY_INPUT, 'final' => true],
            'defaultValue'      => ['type' => TypeService::TYPE_ANY],
            'description'       => ['type' => TypeService::TYPE_STRING],
            'isDeprecated'      => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
        ];
    }

    public function getDefaultValue()
    {
        return $this->get('defaultValue');
    }
}
