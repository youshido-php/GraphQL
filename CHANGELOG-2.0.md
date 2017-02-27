# Changelog

### v2.0 – ExecutionContext and ResolveInfo Refactor

#### Backward Compatibility Break
1. Processor became more flexible and expandable and now accepts `ExecutionContext` instead of Schema.
2. `resolve` function signature has been changed, now it's possible to tailor it to the specific framework and have proper type hinting (like with Symfony DI Container), but the trade off is that now it accepts the `ResolveInfoInterface`.

If you want to upgrade, almost an easy solution would be to:
1. Find and Replace in all project: 
    * `new Processor($schema)` replace with `new Processor(new ExecutionContext($schema))`
    * `ResolveInfo $info` replace with `ResolveInfoInterface $info`
    * `use Youshido\GraphQL\Execution\ResolveInfo` replace with `use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface`

#### General changes
- Relay support added
- Test coverage 100% added
- `AbstractField` was introduced and `AbstractType` was changed (see [upgrade-1.2](UPGRADE-1.2.md))

#### Processor
- Processor now requires an `AbstractSchema` in `__construct`
- `->processRequest($payload, $variables = [])` changed to `->processPayload(string $payload, $variables = [])`

#### Type
- parameter `resolve` was removed from config
- parameter `args` was removed from config
- parameter `fields` is now required when you create an instance of `ObjectType`

#### Field
- `AbstractField` was introduced
- `Field` class now has to be used to define a field inside `fields` config
- abstract `resolve` methods added
