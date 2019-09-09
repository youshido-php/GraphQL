<?php

namespace BlogTest;

if(file_exists(__DIR__ . '/../../../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../../../vendor/autoload.php';
} else {
    require_once realpath(__DIR__ . '/../..') . '/vendor/autoload.php';
}

require_once __DIR__ . '/Schema/DataProvider.php';
require_once __DIR__ . '/Schema/PostType.php';
require_once __DIR__ . '/Schema/PostStatus.php';
require_once __DIR__ . '/Schema/ContentBlockInterface.php';
require_once __DIR__ . '/Schema/LikePostField.php';
require_once __DIR__ . '/Schema/BannerType.php';
require_once __DIR__ . '/Schema/ContentBlockUnion.php';
require_once __DIR__ . '/Schema/PostInputType.php';
require_once __DIR__ . '/Schema/BlogSchema.php';
