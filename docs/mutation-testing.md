# Mutation Testing

> Note: Before you can start using mutation testing, you need to have a successfully running test suite.

**Source code**: [github.com/pestphp/pest-plugin-mutate](Pest Plugin Mutate)

To start using Pest's Mutation plugin, you need to require the plugin via Composer.

```bash
composer require pestphp/pest-plugin-mutate --dev
```

<a name="run-mutation-testing"></a>
## Run mutation testing

### Run from CLI

You can run the mutation testing from the CLI providing the `--mutate` option.

```bash
vendor/bin/pest --mutate
```

By default, it uses the default [configuration](#configuration) (if available) or you can use a different configuration by providing its name.

```bash
vendor/bin/pest --mutate="arithmetic only"
```

You can set or overwrite all [options](#options) available.

```bash
vendor/bin/pest --mutate --path=src
```

Additionally, you can make use of most of the options available in Pest. \
For example this would only run mutation tests for code covered by tests in the "unit" group.

```bash
vendor/bin/pest --mutate --covered-only --group=unit
```

### `mutate()`

You can use the `mutate()` function on test cases or describe blocks to run the mutations only for code covered by the given test or describe block.

This function is intended to be used in your daily development workflow to establish a mutation testing practice right when you are implementing or modifying a feature.

By default, it inherits the default configuration. You can change this by providing an alternative configuration name.

```php
test('sum', function () {
  $result = sum(1, 2);

  expect($result)->toBe(3);
})->mutate(); // or ->mutate('arithmetic only')
```

Executing the `./vendor/bin/pest` command will now automatically run mutation testing. It is not necessary to provide the `--mutate` option.

You can append options after calling `mutate()`.

```php
test('sum', function () {
  $result = sum(1, 2);

  expect($result)->toBe(3);
})->mutate()
  ->path('src/functions.php');
```

> Using the `mutate()` function on a test case or describe block will only create mutations for code covered by the given test or describe block.
> To disable this behaviour append `->coveredOnly(false)` or execute Pest with the `--covered-only=false` option (`vendor/bin/pest --mutate --covered-only=false`).

<a name="configuration"></a>
## Configuration

You can globally configure mutation testing in you Pest.php file.

```php
mutate()
    ->paths('src');
```

For all the available options see [Options](#options) section.

<a name="configurations"></a>
### Alternative configurations

You can create multiple mutation testing configurations.

```php
use Pest\Mutate\Mutators;

mutate('arithmetic only') // 'default' if not provided
    ->paths('src')
    ->mutators(Mutators::SET_ARITHMETIC);
```

And you can inherit from another configuration.

> WIP: Configuration inheritance is not implemented yet!

```php
use Pest\Mutate\Mutators;

mutate('arithmetic only')
    ->extends('default')
    ->mutators(Mutators::SET_ARITHMETIC);
```

<a name="options"></a>
## Options

The following options are available.

<div class="collection-method-list" markdown="1">

- [`path()`](#path)
- [`class()`](#class)
- [`mutator()`](#mutator)
- [`except()`](#except)
- [`coveredOnly()`](#coveredOnly)
- [`uncommittedOnly()`](#uncommittedOnly)
- [`changedOnly()`](#changedOnly)
- [`stopOnSurvival()`](#stopOnSurvival)
- [`stopOnUncovered()`](#stopOnUncovered)
- [`bail()`](#bail)

</div>

---


<a name="options-path"></a>
### `path()`
CLI: `--path`

Limit the directories or files to mutate by providing one or more paths to a directory or file to test.

You can also use patterns.

```php
mutate()
    ->path('src');
```


<a name="options-class"></a>
### `class()`
CLI: `--class`

Limit the mutations to one or more classes by providing one or more class names.

```php
mutate()
    ->class(MyClass::class, OtherClass::class);
```


<a name="options-mutator"></a>
### `mutator()`
CLI: `--mutator`

Choose the mutators you want to use. Choose from various sets or provide individual mutators. If not set, `Mutators::SET_DEFAULT` is used.

A list of all available mutators can be found in the [Mutator Reference](/docs/mutator-reference).

```php
use Pest\Mutate\Mutators;

mutate()
    ->mutator(Mutators::SET_ARITHMETIC);
// or
mutate()
    ->mutator(Mutators::ARITHMETIC_PLUS_TO_MINUS, Mutators::ARITHMETIC_MINUS_TO_PLUS);
```

On the CLI you can provide a comma separated list of mutator names.

```bash
vendor/bin/pest --mutate --mutator=ArithmeticPlusToMinus,ArithmeticMinusToPlus
```


<a name="options-except"></a>
### `except()`
CLI: `--except`

> WIP: The `except()` option is not implemented yet!

Exclude specific mutators from being used. Especially useful if you want to use a set of mutators but want to exclude some of them.

```php
use Pest\Mutate\Mutators;

mutate()
    ->mutators(Mutators::SET_ARITHMETIC);
    ->except(Mutators::ARITHMETIC_PLUS_TO_MINUS);
```


<a name="options-covered-only"></a>
### `coveredOnly()`
CLI: `--covered-only`

Limit mutations to code that is covered by tests. This is especially helpful if you are running only a subset of your test suite. See [Only run parts of your test suite](#only-run-parts-of-your-test-suite).

```php
mutate()
    ->coveredOnly();
```


<a name="options-uncommitted-only"></a>
### `uncommittedOnly()`
CLI: `--uncommitted-only`

> WIP: The `uncommittedOnly()` option is not implemented yet!

Limit mutations to code that has uncommitted changes.

```php
mutate()
    ->uncommittedOnly();
```


<a name="options-changed-only"></a>
### `changedOnly()`
CLI: `--changed-only`

> WIP: The `changedOnly()` option is not implemented yet!

Limit mutations to code that has changed relative to a common ancestor of the given branch (defaults to `main`).

```php
mutate()
    ->changedOnly(); // or ->changedOnly('master');
```


<a name="options-stop-on-survival"></a>
### `stopOnSurvival()`
CLI: `--stop-on-survival`

Stop execution upon first survived mutant.

```php
mutate()
    ->stopOnSurvival();
```


<a name="options-stop-on-uncovered"></a>
### `stopOnUncovered()`
CLI: `--stop-on-uncovered`

Stop execution upon first uncovered mutant.

```php
mutate()
    ->stopOnUncovered();
```


<a name="options-bail"></a>
### `bail()`
CLI: `--bail`

Stop execution upon first uncovered or survived mutant.

```php
mutate()
    ->bail();
```

## Performance

Mutation testing is potentially very time-consuming and resource intensive because of the sheer amount of possible mutations and tests to run them against.

Therefore, Pest Muation Testing is optimized to limit the amount of mutations and tests to run against as much as possible. To achieve this, it uses the following strategies:
- Limit the amount of possible mutations by having a carefully chosen set of mutators
- Run only tests that covers the mutated code
- It tries to reuse cached mutations
- _Run tests in a decent order (fastet test first)_ - (not implemented yet)

But there is much more you can do to improve performance. Especially if you have a larger code base and/or you are using mutations testing while developing locally.

### Use a code coverage driver

If you have a code coverage driver available, Pest will use it to only run tests that cover the mutated code.

**Supports [XDebug 3.0+](https://xdebug.org/docs/install/)** or [PCOV](https://github.com/krakjoe/pcov).

### Reduce the number of mutators

Reduce the number of mutations by choosing a smaller set of mutators.

```php
vendor/bin/pest --mutate --mutator=ArithmeticPlusToMinus
```

### Reduce the number of files to mutate

Reduce the number of mutations by only mutating a subset of your code base.

```bash
vendor/bin/pest --mutate --path=src/path/file.php
```

### Reduce the number of classes to mutate

Reduce the number of mutations by only mutating a subset of your classes.

```bash
vendor/bin/pest --mutate --class="App\\MyClass,App\\OtherClass"
```

<a name="only-run-parts-of-your-test-suite"></a>
### Only run parts of your test suite

Reduce the number of mutations and tests to execute by only running a subset of your test suite.

```bash
vendor/bin/pest --mutate --filter=SumTest
```

For more filter options see [Filtering](https://pestphp.com/docs/filtering-tests#content---bail).

### Only create mutations for covered files / lines

Reduce the number of mutations by only mutating code that is covered by tests. This is especially helpful if you are running only a subset of your test suite. See [Only run parts of your test suite](#only-run-parts-of-your-test-suite).

```bash
vendor/bin/pest --mutate --covered-only
```

> Attention: Code not covered by tests will not be mutated. Ensure your test suite covers all code you want to mutate.

### Run tests in parallel

```bash
vendor/bin/pest --mutate --parallel
```

## Ignoring Mutations

Sometimes, you may want to ignore a specific mutation or line of code. To do so, you may use the `@pest-ignore-mutation` annotation:

```php
    if($age >= 18) // @pest-ignore-mutation
        // ...
    ];
}
```

## Minimum Threshold Enforcement

> WIP: The `min()` option is not implemented yet!

Just like code coverage, mutation coverage can also be enforced. You can use the `--mutate` and `--min` options to define the minimum threshold values for the mutation score index. If the specified thresholds are not met, Pest will report a failure.

```bash
./vendor/bin/pest --mutate --min=100
```

## Custom Mutators

You may want to create your own custom mutators. You can do so by creating a class that implements the `Mutator` interface. \
This example will remove `use` statements.

```php
namespace App\Mutators;

use Pest\Mutate\Contracts\Mutator;
use PhpParser\Node;
use PhpParser\NodeTraverser;

class RemoveUseStatement implements Mutator
{
    public static function can(Node $node): bool
    {
         return $node instanceof Node\Stmt\Use_;
    }

    public static function mutate(Node $node): int
    {
        return NodeTraverser::REMOVE_NODE;
    }
}
```

Afterward you can use your mutator.

```php
use App\Mutators\RemoveUseStatement;

mutate()
    ->mutators(RemoveUseStatement::class);
```

In the CLI you must provide the full class name.

```bash
vendor/bin/pest --mutate --mutators="App\\Mutators\\RemoveUseStatement"
```