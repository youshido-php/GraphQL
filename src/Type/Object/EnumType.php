<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Type\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Type\Traits\FinalTypesConfigTrait;

final class EnumType extends AbstractEnumType
{
    use FinalTypesConfigTrait;
    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
        }

        $this->config = new EnumTypeConfig($config, $this);
    }

    public function getValues()
    {
        return $this->getConfig()->getValues();
    }

}
