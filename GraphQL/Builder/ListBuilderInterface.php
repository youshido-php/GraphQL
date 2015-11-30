<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Builder;


use Youshido\GraphQL\Field;
use Youshido\GraphQL\Type\TypeInterface;

interface ListBuilderInterface
{

    /**
     * @param                       $name
     * @param TypeInterface|string  $type
     * @param array                 $config
     *
     * @return  ListBuilderInterface
     */
    public function add($name, $type, $config = []);

    /**
     * @param $name
     * @return Field|null
     */
    public function get($name);

    /**
     * @return Field[]
     */
    public function all();

    /**
     * @param $name
     *
     * @return Field
     */
    public function has($name);
}