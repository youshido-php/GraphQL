<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Type\Object\AbstractEnumType;

class EpisodeEnum extends AbstractEnumType
{

    public function getValues()
    {
        return [
            'NEWHOPE' => [
                'value'       => 4,
                'description' => 'Released in 1977.'
            ],
            'EMPIRE'  => [
                'value'       => 5,
                'description' => 'Released in 1980.'
            ],
            'JEDI'    => [
                'value'       => 6,
                'description' => 'Released in 1983.'
            ],
        ];
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return 'Episode';
    }
}