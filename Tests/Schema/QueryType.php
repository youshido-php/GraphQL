<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class QueryType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
    }

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('hero', new CharacterInterface(), [
                'args' => [
                    'episode' => ['type' => new EpisodeEnum()]
                ]
            ])
            ->addField('human', new HumanType())
            ->addField('droid', new DroidType());
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return 'Query';
    }
}