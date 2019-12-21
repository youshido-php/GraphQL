# Changelog

Please update this file right before tagging a new release

## v1.7.1

* relaxed symfony/property-accessor version constraints so the package can be installed in Symfony 4.x projects

## v1.7.0

* fix some README badges
* add a more robust implementation for `TypeService::getPropertyValue()`
* fix PhpStorm inspection performance complaints
* fix a bug that prevented using `null` as a default value
* add support for error extensions
* throw `ConfigurationException` when trying to add two types with the same name to the schema
* add a `getImplementations()` method to `AbstractInterfaceType`, can be used to properly discover all possible types during introspection
* run Travis CI on PHP 7.2 and 7.3 too
* run phpstan static analysis (level 1 only) during Travis CI builds
* rename the `Tests` directory to `tests` for consistency with other projects
* remove some obsolete documentation

## v1.6.0

* fix the Travis CI build for PHP 5.5
* improve examples somewhat
* make `Node` stricter when validating input
* return null not just when the field is scalar but whenever it's non-null

## v1.5.5

* add missing directive locations
* add a `totalCount` field to connections
* fix a regression introduced in #151
* add a type/field lookup cache for improved performance
* add support for nested deferred resolvers
* properly handle null values in `BooleanType`
