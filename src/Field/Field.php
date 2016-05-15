<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class Field
 * @package Youshido\GraphQL\Type\Field
 *
 */
final class Field extends AbstractField
{

    protected $isFinal = true;

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        /** we can access config directly here because of __construct architecture */
        return $this->config->get('type');
    }

    public function getName()
    {
        return $this->getConfigValue('name', null);
    }

}
