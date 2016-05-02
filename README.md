# GraphQL
[![Build Status](https://travis-ci.org/Youshido/GraphQL.svg?branch=master)](http://travis-ci.org/Youshido/GraphQL)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Youshido/GraphQL/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Youshido/GraphQL/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Youshido/GraphQL/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Youshido/GraphQL/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8b8ab2a2-32fb-4298-a986-b75ca523c7c9/mini.png)](https://insight.sensiolabs.com/projects/8b8ab2a2-32fb-4298-a986-b75ca523c7c9)

This is a clean PHP realization of the GraphQL protocol based on the working draft of the specification located on https://github.com/facebook/graphql.
 
GraphQL is a modern replacement of the REST API approach. It advanced in many ways and has fundamental improvements over the old not-so-good REST:

 - self-checks embedded on the ground level of your backend architecture  
 - reusable API for different client versions and devices – no need in "/v1" and "/v2" anymore 
 - a complete new level of distinguishing the backend and the frontend logic
 - easily generated documentation and incredibly easy way to explore API for other developers
 - once your architecture is complete – simple changes on the client does not require you to change API
 
It could be hard to believe, but give it a try and you'll be rewarded with much better architecture and so much easier to support code. 
 
_Current package is and will be trying to be kept up with the latest revision of the GraphQL specification which is now of April 2016._
 
## Getting started

You should be better off starting with some examples, and "Star Wars" become a somewhat "Hello world" example for the GraphQL frameworks.
We have that too and if you looking for just that – go directly by this link – [Star Wars example](https://github.com/Youshido/GraphQL/Tests/StarWars).
On the other hand based on the feedback we prepared a step-by-step for those who want to get up to speed fast.
 
### Installation

You should simply install this package using the composer. If you're not familiar with it you should check out the [manual](https://getcomposer.org/doc/00-intro.md).
Add the following package to your `composer.json`:

 ```
 {
     "require": {
         "youshido/graphql": "*"
     }
 }
 ```
After you have created the `composer.json` simply run `composer update`.
Alternatively you can do the following sequence:
```sh
mkdir graphql-test && cd graphql-test
composer init
composer require youshido/graphql
```
 
After the successful message, Let's check if everything is good by setting up a simple schema that will return current time.
_(you can find this example in the examples directory – [01_sandbox](https://github.com/Youshido/GraphQL/examples/01_sandbox))_

```php
<?php
namespace Sandbox;

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

require_once 'vendor/autoload.php';

$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => new ObjectType([
        'name' => 'RootQueryType',
        'fields' => [
            'currentTime' => [
                'type' => new StringType(),
                'resolve' => function() {
                    return date('Y-m-d H:ia');
                }
            ]
        ]
    ])
]));

$res = $processor->processRequest('{ currentTime }')->getResponseData();
print_r($res);
```

If everything was set up correctly – you should see response with your current time:
 ```js
 { 
    data: { currentTime: "2016-05-01 19:27pm" }
 }
 ```
 
If not – check that you have the latest composer version and that you've created your test file in the same directory you have `vendor` folder in.
You can always use a script from `examples` folder. Simply run `php vendor/youshido/GraphQL/examples/01_sandbox/index.php`.

## Understanding by example

For our learning example we'll architect a GraphQL Schema for the Blog.

We'll keep it simple so our Blog will have Users who write Posts and leave Comments.
Here's an example of the query that returns title and summary of the latest Post:
 ```
 latestPost {
     title,
     summary
 }
 ```
As you can see, GraphQL query is a simple text query structured very much similar to the json or yaml format.

Supposedly our server should reply with a response structured like following:
 ```js
 {
    data: {
        latestPost: {
            title: "Interesting approach",
            summary: "This new GraphQL library for PHP works really well"
        }
    }
 }
 ```

Let's go ahead and create a backend that can handle that.

### Creating Post schema

We believe you'll be using our package along with your favorite framework (we have a Symfony version [here](http://github.com/Youshido/GraphqlBundle)).
But for the purpose of the current example we'll keep it as plain php code.
_(you can check out the complete example by the following link – (https://github.com/Youshido/GraphQL/examples/02_Blog)_

We'll take a quick look on different approaches you can use to define your schema. 
Even though inline approach might seem to be easier and faster we strongly recommend to use an object based because it will give you more flexibility and freedom as your project grows.

#### Inline approach

So we'll start by creating the Post type. For now we'll have only two fields – title and summary:

*index.php*
```php
//...
$rootQueryType = new ObjectType([               // all GraphQL types will be extended from the base types
    'name'   => 'RootQueryType',                // this is the main query structure, name doesn't really matter but by a convention we name it RootQueryType
    'fields' => [
        'latestPost' => new ObjectType([        // our Post type will be extended from the generic ObjectType 
            'name'    => 'Post',                // name of our type
            'fields'  => [                  
                'title'   => new StringType(),  // defining the "title" field, type - string
                'summary' => new StringType(),  // defining the "summary" field, type is also string
            ],
            'resolve' => function () {          // this is a resolve function, for now it will return a static array with data
                return [
                    "title"   => "New approach in API has been revealed",
                    "summary" => "This post will describe a new approach to create and maintain APIs",
                ];
            }
        ])
    ]
]);
//...
```

