<?php
/**
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 4/23/17 11:10 PM
 */

namespace Examples\BookStore;


class DataProvider
{

    static private $authors = [
        '1' => [
            'id' => '1',
            'firstName' => 'Mark',
            'lastName' => 'Twain'
        ]
    ];

    public static function getBooks()
    {
        return [
            [
                'id' => 1,
                'title' => 'The Adventures of Tom Sawyer',
                'year' => 1876,
                'isbn' => '978-0996584838',
                'author' => self::$authors['1']
            ],
        ];
    }

    public static function getAuthors()
    {
        return self::$authors;
    }
}