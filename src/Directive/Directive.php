<?php
/**
 * Date: 3/17/17
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
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

    public function build(DirectiveConfig $config)
    {

    }

    public function addArguments($argumentsList)
    {
        return $this->getConfig()->addArguments($argumentsList);
    }

}
