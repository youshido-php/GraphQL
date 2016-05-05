import {
    graphql,
    GraphQLSchema,
    GraphQLObjectType,
    GraphQLString
} from 'graphql';
import { } from 'express-graphql';

var schema = new GraphQLSchema({
    query: new GraphQLObjectType({
        name: 'RootQueryType',
        fields: {
            hello: {
                type: GraphQLString,
                resolve() {
                    return 'world';
                }
            }
        }
    })
});

var query = '{ hello }';

graphql(schema, query).then(result => {
    console.log(result);
});