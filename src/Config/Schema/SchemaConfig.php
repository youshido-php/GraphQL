<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:53 PM
*/

namespace Youshido\GraphQL\Config\Schema;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class SchemaConfig extends AbstractConfig
{

    public function getRules()
    {
        return [
            'query'    => ['type' => TypeService::TYPE_OBJECT_TYPE, 'required' => true],
            'mutation' => ['type' => TypeService::TYPE_OBJECT_TYPE],
            'name'     => ['type' => TypeService::TYPE_STRING],
        ];
    }

    /**
     * @return ObjectType
     */
    public function getQuery()
    {
        return $this->data['query'];
    }

    /**
     * @param $query AbstractObjectType
     *
     * @return SchemaConfig
     */
    public function setQuery($query)
    {
        $this->data['query'] = $query;

        return $this;
    }

    /**
     * @return ObjectType
     */
    public function getMutation()
    {
//        if (!$this->get('mutation')) {
//            $this->set('mutation', new ObjectType(['name' => $this->getName()]));
//        }
        return $this->get('mutation');
    }

    /**
     * @param $query AbstractObjectType
     *
     * @return SchemaConfig
     */
    public function setMutation($query)
    {
        $this->data['mutation'] = $query;

        return $this;
    }

    public function getName()
    {
        return $this->get('name', 'RootSchema');
    }


}
