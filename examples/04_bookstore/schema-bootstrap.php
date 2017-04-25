<?php

if (is_file(__DIR__ . '/../../../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../../../vendor/autoload.php';
}
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/Type/BookType.php';
require_once __DIR__ . '/Schema/Type/AuthorType.php';
require_once __DIR__ . '/Schema/Type/CategoryType.php';
require_once __DIR__ . '/Schema/Field/Book/RecentBooksField.php';
require_once __DIR__ . '/Schema/Field/CategoriesField.php';
require_once __DIR__ . '/DataProvider.php';
require_once __DIR__ . '/Schema/BookStoreSchema.php';
