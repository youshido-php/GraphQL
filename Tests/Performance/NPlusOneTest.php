<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 2/5/17 12:23 PM
*/

namespace Youshido\Tests\Performance;


use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class NPlusOneTest extends \PHPUnit_Framework_TestCase
{

    private function getDataForPosts()
    {
        /**
         * We could make a DB request here, as a simplified version:
         * SELECT * FROM posts p LEFT JOIN authors a ON (a.id = p.author_id) LIMIT 10, 10
         */
        $authors = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Alex'],
            ['id' => 3, 'name' => 'Mike'],
        ];
        $posts   = [];
        for ($i = 0; $i < 10; $i++) {
            $posts[] = [
                'id'     => $i + 1,
                'title'  => sprintf('Post title $%s', $i),
                'author' => $authors[$i % 3]
            ];
        }

        return $posts;
    }

    public function testHigherResolver()
    {
        $authorType = new ObjectType([
            'name'   => 'Author',
            'fields' => [
                'id'   => new IdType(),
                'name' => new StringType(),
            ]
        ]);

        $postType = new ObjectType([
            'name'   => 'Post',
            'fields' => [
                'id'     => new IntType(),
                'title'  => new StringType(),
                'author' => $authorType,
            ]
        ]);

        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'fields' => [
                    'posts' => [
                        'type'    => new ListType($postType),
                        'resolve' => function ($source, $args, $info) {
                            return $this->getDataForPosts();
                        }
                    ]
                ]
            ])
        ]));

        $data = $processor->processPayload('{ posts { id, title, author { id, name } } }')->getResponseData();
        $this->assertNotEmpty($data['data']['posts'][0]['author']);
    }

}