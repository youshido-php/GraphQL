<?php

namespace Youshido\GraphQL\Config\Field;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class FieldConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string $description)
 * @method string|null getDescription()
 *
 * @method TypeInterface getType()
 * @method void setType(TypeInterface $type)
 *
 * @method callable getResolve()
 * @method setResolve(callable $resolve)
 *
 * @method void setIsDeprecated(bool $isDeprecated)
 * @method bool isDeprecated()
 *
 * @method void setDeprecationReason(string $deprecationReason)
 * @method string getDeprecationReason()
 *
 * @method void setCost(int|callable $cost)
 * @method int|callable getCost()
 */
class FieldConfig extends AbstractConfig
{
    use ArgumentsAwareConfigTrait;

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'              => ['type' => PropertyType::TYPE_STRING, 'required' => true],
            'type'              => ['type' => PropertyType::TYPE_GRAPHQL_TYPE, 'required' => true],
            'args'              => ['type' => PropertyType::TYPE_INPUT_FIELD, 'array' => true],
            'description'       => ['type' => PropertyType::TYPE_STRING],
            'resolve'           => ['type' => PropertyType::TYPE_CALLABLE],
            'isDeprecated'      => ['type' => PropertyType::TYPE_BOOLEAN, 'default' => false],
            'deprecationReason' => ['type' => PropertyType::TYPE_STRING],
            'cost'              => ['type' => PropertyType::TYPE_COST],
        ];
    }

    /**
     * Configure class properties
     */
    protected function build()
    {
        $this->buildArguments();
    }
}
