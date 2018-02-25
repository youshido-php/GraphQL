<?php
/**
 * Date: 3/17/17
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Directive;


use Youshido\GraphQL\Type\AbstractType;

interface DirectiveInterface
{

    public function getName();

    public function addArguments($argumentsList);

    public function removeArgument($argumentName);

    public function addArgument($argument, $ArgumentInfo = null);

    /**
     * @return AbstractType[]
     */
    public function getArguments();

    /**
     * @param string $argumentName
     *
     * @return AbstractType
     */
    public function getArgument($argumentName);

    /**
     * @param string $argumentName
     *
     * @return bool
     */
    public function hasArgument($argumentName);

    /**
     * @return boolean
     */
    public function hasArguments();

}
