# Upgrade to 1.2
 
 We made important changes to the structure of the GraphQL Schema to be more consistent with the original specification and to be able to implement Relay.
 
 
 Processor cannot really exist without the Schema, so it was moved to constructur:  
 Before:
 ```php
 $processor = new Processor();
 $processor->setSchema(new Schema([
     'query' => $rootQueryType
 ]));
 ```
 After:
 ```php
 $processor = new Processor((new Schema([
     'query' => $rootQueryType
 ]));
 ```
 
 
 Schema definition has changed so that `resolve` function is no longer exists inside `Type` and instead moved to where it belongs – to the `Field` object that was introduced in `1.2`.
 Before:
 ```php
 $userType = new ObjectType([
    'name' => 'User',
    'fields' => [
        'id' => new IntType(),
        'name' => new StringType(),
    ],
    'resolve' => function($value, $args, $type) {
        return [
            'id' => 1,
            'name' => 'John'
        ];
    }
 ]);  
 
 ```
