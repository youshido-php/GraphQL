<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\ListType;


use Youshido\GraphQL\Type\Config\Object\ListTypeConfig;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractListType extends AbstractObjectType
{

    public function __construct($config = [])
    {
        if (empty($config) && (get_class($this) != 'Youshido\GraphQL\Type\Object\ListType')) {
            $config['name'] = $this->getName();
            $config['item'] = $this->getItem();
        }

        $this->config = new ListTypeConfig($config, $this);
    }

    abstract public function getName();

    abstract public function getItem();

    public function isValidValue($value)
    {
        return is_array($value);
    }

    protected function build(TypeConfigInterface $config)
    {

    }

    final public function getKind()
    {
        return TypeMap::KIND_LIST;
    }

}