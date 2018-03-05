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
 * created: 5/4/16 9:18 PM
 */

namespace Youshido\GraphQL\Type\Traits;

use Youshido\GraphQL\Field\FieldInterface;

/**
 * Class AutoNameTrait.
 */
trait AutoNameTrait
{
    public function getName()
    {
        if (!empty($this->config)) {
            return $this->config->getName();
        }

        $className = \get_called_class();

        if ($prevPos = \mb_strrpos($className, '\\')) {
            return \mb_substr($className, $prevPos + 1);
        }

        if ('Field' === \mb_substr($className, -5)) {
            return \lcfirst(\mb_substr($className, 0, -5));
        }

        if ('Type' === \mb_substr($className, -4)) {
            return \mb_substr($className, 0, -4);
        }

        if ($this instanceof FieldInterface) {
            return \lcfirst($className);
        }

        return $className;
    }
}
