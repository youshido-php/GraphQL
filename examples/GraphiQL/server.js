/**
 *  Copyright (c) 2015, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 */
'use strict';

var _interopRequireDefault = require('babel-runtime/helpers/interop-require-default')['default'];

var _express = require('express');

var _express2 = _interopRequireDefault(_express);

var _expressGraphql = require('express-graphql');

var _expressGraphql2 = _interopRequireDefault(_expressGraphql);

var _graphql = require('graphql');

var app = (0, _express2['default'])();
app.use(_express2['default']['static'](__dirname));
app.use('/graphql', (0, _expressGraphql2['default'])(function () {
  return {
    schema: TestSchema
  };
}));
app.listen(8080);
console.log('Started on http://localhost:8080/');

// Schema defined here

// Test Schema

var TestEnum = new _graphql.GraphQLEnumType({
  name: 'TestEnum',
  values: {
    RED: { description: 'A rosy color' },
    GREEN: { description: 'The color of martians and slime' },
    BLUE: { description: 'A feeling you might have if you can\'t use GraphQL' }
  }
});

var TestInputObject = new _graphql.GraphQLInputObjectType({
  name: 'TestInput',
  fields: function fields() {
    return {
      string: {
        type: _graphql.GraphQLString,
        description: 'Repeats back this string'
      },
      int: { type: _graphql.GraphQLInt },
      float: { type: _graphql.GraphQLFloat },
      boolean: { type: _graphql.GraphQLBoolean },
      id: { type: _graphql.GraphQLID },
      'enum': { type: TestEnum },
      object: { type: TestInputObject },
      // List
      listString: { type: new _graphql.GraphQLList(_graphql.GraphQLString) },
      listInt: { type: new _graphql.GraphQLList(_graphql.GraphQLInt) },
      listFloat: { type: new _graphql.GraphQLList(_graphql.GraphQLFloat) },
      listBoolean: { type: new _graphql.GraphQLList(_graphql.GraphQLBoolean) },
      listID: { type: new _graphql.GraphQLList(_graphql.GraphQLID) },
      listEnum: { type: new _graphql.GraphQLList(TestEnum) },
      listObject: { type: new _graphql.GraphQLList(TestInputObject) }
    };
  }
});

var TestInterface = new _graphql.GraphQLInterfaceType({
  name: 'TestInterface',
  description: 'Test interface.',
  fields: function fields() {
    return {
      name: {
        type: _graphql.GraphQLString,
        description: 'Common name string.'
      }
    };
  },
  resolveType: function resolveType(check) {
    return check ? UnionFirst : UnionSecond;
  }
});

var UnionFirst = new _graphql.GraphQLObjectType({
  name: 'First',
  fields: function fields() {
    return {
      name: {
        type: _graphql.GraphQLString,
        description: 'Common name string for UnionFirst.'
      },
      first: {
        type: new _graphql.GraphQLList(TestInterface),
        resolve: function resolve() {
          return true;
        }
      }
    };
  },
  interfaces: [TestInterface]
});

var UnionSecond = new _graphql.GraphQLObjectType({
  name: 'Second',
  fields: function fields() {
    return {
      name: {
        type: _graphql.GraphQLString,
        description: 'Common name string for UnionFirst.'
      },
      second: {
        type: TestInterface,
        resolve: function resolve() {
          return false;
        }
      }
    };
  },
  interfaces: [TestInterface]
});

var TestUnion = new _graphql.GraphQLUnionType({
  name: 'TestUnion',
  types: [UnionFirst, UnionSecond],
  resolveType: function resolveType() {
    return UnionFirst;
  }
});

var TestType = new _graphql.GraphQLObjectType({
  name: 'Test',
  fields: function fields() {
    return {
      test: {
        type: TestType,
        description: '`test` field from `Test` type.',
        resolve: function resolve() {
          return {};
        }
      },
      union: {
        type: TestUnion,
        description: '> union field from Test type, block-quoted.',
        resolve: function resolve() {
          return {};
        }
      },
      id: {
        type: _graphql.GraphQLInt,
        description: 'id field from Test type.',
        resolve: function resolve() {
          return {};
        }
      },
      isTest: {
        type: _graphql.GraphQLBoolean,
        description: 'Is this a test schema? Sure it is.',
        resolve: function resolve() {
          return true;
        }
      },
      hasArgs: {
        type: _graphql.GraphQLString,
        resolve: function resolve(value, args) {
          return JSON.stringify(args);
        },
        args: {
          string: { type: _graphql.GraphQLString },
          int: { type: _graphql.GraphQLInt },
          float: { type: _graphql.GraphQLFloat },
          boolean: { type: _graphql.GraphQLBoolean },
          id: { type: _graphql.GraphQLID },
          'enum': { type: TestEnum },
          object: { type: TestInputObject },
          // List
          listString: { type: new _graphql.GraphQLList(_graphql.GraphQLString) },
          listInt: { type: new _graphql.GraphQLList(_graphql.GraphQLInt) },
          listFloat: { type: new _graphql.GraphQLList(_graphql.GraphQLFloat) },
          listBoolean: { type: new _graphql.GraphQLList(_graphql.GraphQLBoolean) },
          listID: { type: new _graphql.GraphQLList(_graphql.GraphQLID) },
          listEnum: { type: new _graphql.GraphQLList(TestEnum) },
          listObject: { type: new _graphql.GraphQLList(TestInputObject) }
        }
      }
    };
  }
});

var TestMutationType = new _graphql.GraphQLObjectType({
  name: 'MutationType',
  description: 'This is a simple mutation type',
  fields: {
    setString: {
      type: _graphql.GraphQLString,
      description: 'Set the string field',
      args: {
        value: { type: _graphql.GraphQLString }
      }
    }
  }
});

var TestSubscriptionType = new _graphql.GraphQLObjectType({
  name: 'SubscriptionType',
  description: 'This is a simple subscription type',
  fields: {
    subscribeToTest: {
      type: TestType,
      description: 'Subscribe to the test type',
      args: {
        id: { type: _graphql.GraphQLString }
      }
    }
  }
});

var TestSchema = new _graphql.GraphQLSchema({
  query: TestType,
  mutation: TestMutationType,
  subscription: TestSubscriptionType
});
