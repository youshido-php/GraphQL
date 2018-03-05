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
 * Date: 3/17/17.
 */

namespace Youshido\GraphQL\Directive;

use Youshido\GraphQL\Config\Directive\DirectiveConfig;
use Youshido\GraphQL\Type\Traits\ArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;

class Directive implements DirectiveInterface
{
    use ArgumentsAwareObjectTrait;
    use AutoNameTrait;

    protected $isFinal = false;

    public function __construct(array $config = [])
    {
        if (empty($config['name'])) {
            $config['name'] = $this->getName();
        }

        $this->config = new DirectiveConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    public function build(DirectiveConfig $config): void
    {
    }

    public function addArguments($argumentsList)
    {
        return $this->getConfig()->addArguments($argumentsList);
    }
}
