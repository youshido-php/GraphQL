<?php
/**
 * Date: 17.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;


use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;

interface FieldInterface
{
    /**
     * @return AbstractType
     */
    public function getType();

    public function getName();

    public function addArguments($argumentsList);

    public function removeArgument($argumentName);

    public function addArgument($argument, $ArgumentInfo = null);

    /**
     * @return AbstractType[]
     */
    public function getArguments();

    /**
     * @return AbstractType
     */
    public function getArgument($argumentName);

    /**
     * @return boolean
     */
    public function hasArgument($argumentName);

    /**
     * @return boolean
     */
    public function hasArguments();

    public function resolve($value, array $args, ResolveInfo $info);

    public function getResolveFunction();
}
