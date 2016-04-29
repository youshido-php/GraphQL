# GraphQL
[![Build Status](https://travis-ci.org/Youshido/GraphQL.svg?branch=master)](http://travis-ci.org/Youshido/GraphQL)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Youshido/GraphQL/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Youshido/GraphQL/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Youshido/GraphQL/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Youshido/GraphQL/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8b8ab2a2-32fb-4298-a986-b75ca523c7c9/mini.png)](https://insight.sensiolabs.com/projects/8b8ab2a2-32fb-4298-a986-b75ca523c7c9)

This is a pure PHP realization of the GraphQL protocol based on the working draft of the specification located on https://github.com/facebook/graphql.
 
GraphQL is a modern replacement of the REST API approach. It advanced in a lof ot ways and has fundamental advantages:
 - reusable API for different versions and devices
 - a complete new level of distinguishing the backend and the frontend logic

 
Current package is and will be trying to keep up with the latest revision of the GraphQL specification which is of April 2016 currently.
 
## Getting started

You should be better off starting with some examples, and everybody's using Star Wars trilogy as a domain of knowledge in their examples.
We have that too and if you looking for just that – go directly by this link – [Star Wars example](http://github.com/youshido/graphql/tests/starwars).
On the other hand we believe it's easier to start with something more common so let's get started.
 
### Installation

You should simply install this package using the composer. If you're not familiar with it you should check out the [manual](https://getcomposer.org/doc/00-intro.md).
Add the following package to your `composer.json` and run `composer update`

```
{
    "require": {
        "youshido/graphql": "*"
    }
}
```

## Usage

Let's architect a schema for the simple Blog.

We'll keep our Blog simple but it will surely have Users who write Posts and leave Comments.
If you never seen a GraphQL queries before – it's a simple text query very much similar to the json/yaml format.
Here's an example of the query that returns title and summary of the latest Post:
 ```
 Post(id: "3") {
     title,
     summary
 }
 ```

Supposedly our server should return the following structure:
 ```
 {
    post: {
        title: "Title for id 3",
        summary: "Summary for id 3"
    }
 }
 ```

Now, let's create a schema and the backend that can handle this simple request.

### Creating Post schema

We believe you'll be using our package along with your favorite framework (we have a Symfony version [here](http://github.com/Youshido/GraphqlBundle)).
But for the purpose of the current demonstration we'll keep it as plain php code.
 
*schema.php*
```php



```

 





 