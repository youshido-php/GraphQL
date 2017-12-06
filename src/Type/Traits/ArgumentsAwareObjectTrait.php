<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5:07 PM 5/14/16
 */

namespace Youshido\GraphQL\Type\Traits;


use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;

/**
 * Class ArgumentsAwareObjectTrait
 * @package    Youshido\GraphQL\Type\Traits
 * @codeCoverageIgnore
 *
 * @deprecated To be removed during the release optimization
 */
trait ArgumentsAwareObjectTrait
{
    use ConfigAwareTrait;

    /**
     * @param      $argument
     * @param null $info
     *
     * @return $this
     */
    public function addArgument($argument, $info = null)
    {
        return $this->getConfig()->addArgument($argument, $info);
    }

    public function removeArgument($argumentName)
    {
        return $this->getConfig()->removeArgument($argumentName);
    }

    public function getArguments()
    {
        return $this->getConfig()->getArguments();
    }

    public function getArgument($argumentName)
    {
        return $this->getConfig()->getArgument($argumentName);
    }

    public function hasArgument($argumentName)
    {
        return $this->getConfig()->hasArgument($argumentName);
    }

    public function hasArguments()
    {
        return $this->getConfig()->hasArguments();
    }

}
