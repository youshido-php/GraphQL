<?php
/**
 * PostInputType.php
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Config\InputTypeConfigInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractInputObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class PostInputType extends AbstractInputObjectType
{

    public function build(InputTypeConfigInterface $config)
    {
        $config
            ->addField('title', new NonNullType(new StringType()))
            ->addField('summary', new StringType());
    }


}
