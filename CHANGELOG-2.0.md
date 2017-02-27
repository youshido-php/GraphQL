# Changelog

### v2.0 – ExecutionContext and ResolveInfo Refactor

### Backward Compatibility Break
1. Processor became more flexible and expandable and now accepts `ExecutionContext` instead of Schema.
2. `resolve` function signature has been changed, now it's possible to tailor it to the specific framework and have proper type hinting (like with Symfony DI Container), but the trade off is that now it accepts the `ResolveInfoInterface`.

If you want to upgrade, almost an easy solution would be to:
1. Find and Replace in all project: 
    * `new Processor($schema)` replace with `new Processor(new ExecutionContext($schema))`
    * `ResolveInfo $info` replace with `ResolveInfoInterface $info`
    * `use Youshido\GraphQL\Execution\ResolveInfo` replace with `use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface`


### New Features

New features go here.