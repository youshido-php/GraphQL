<?php

namespace Youshido\GraphQL\Directive;

use Youshido\GraphQL\Config\Directive\DirectiveConfig;
use Youshido\GraphQL\Type\Traits\ArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;

/**
 * Class Directive
 */
class Directive implements DirectiveInterface
{
    use ArgumentsAwareObjectTrait;
    use AutoNameTrait;

    protected $isFinal = false;

    /**
     * Directive constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config['name'])) {
            $config['name'] = $this->getName();
        }

        $this->config = new DirectiveConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    /**
     * @param DirectiveConfig $config
     */
    public function build(DirectiveConfig $config)
    {
    }

    /**
     * @param \Youshido\GraphQL\Type\AbstractType[] $arguments
     */
    public function addArguments($arguments)
    {
        $this->getConfig()->addArguments($arguments);
    }
}
