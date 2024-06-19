# Mutation Testing

> Note: Before you can start using mutation testing, you need to have a successfully running test suite.

**Source code**: [Pest Plugin Mutate](https://github.com/pestphp/pest-plugin-mutate)

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

> When you are working with a larger codebase, checkout the [performance](#performance) section first, otherwise you will not have satisfying experience.

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

Another powerful technique is to call the `mutate()` function directly in your test file. This automatically start mutation testing ones you run `vendor/bin/pest`. Additionally it limits the test run, to the tests in this file.

This function is intended to be used in your daily development workflow to establish a mutation testing practice right when you are implementing or modifying a feature.

By default, it inherits the default configuration. You can change this by providing an alternative configuration name.

> In conjunction with the next release of Pest, it will be possible to append the `mutate()` function direct to an individual test case or a describe block. 

```php
mutate();

test('sum', function () {
  $result = sum(1, 2);

  expect($result)->toBe(3);
})
```

Executing the `./vendor/bin/pest` command will now automatically run mutation testing. It is not necessary to provide the `--mutate` option.

You can append options after calling `mutate()`.

```php
->mutate()
  ->path('src/functions.php')

test('sum', function () {
  $result = sum(1, 2);

  expect($result)->toBe(3);
});
```

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
- [`ignore()`](#ignore)
- [`class()`](#class)
- [`mutator()`](#mutator)
- [`except()`](#except)
- [`coveredOnly()`](#coveredOnly)
- [`uncommittedOnly()`](#uncommittedOnly)
- [`changedOnly()`](#changedOnly)
- [`stopOnEscaped()`](#stopOnEscaped)
- [`stopOnNotCovered()`](#stopOnNotCovered)
- [`bail()`](#bail)
- [`retry()`](#retry)
- [`min()`](#min)
- [`ignoreMinScoreOnZeroMutations()`](#ignoreMinScoreOnZeroMutations)
- [`--id`](#--id)
- [`--no-cache`](#--no-cache)
- [`--clear-cache`](#--clear-cache)

</div>

---


<a name="options-path"></a>
### `path()`
CLI: `--path`

Limit the directories or files to mutate by providing one or more paths to a directory or file to test.

If no paths are provided, it defaults to the source directories configured in your `phpunit.xml` file.

```php
mutate()
    ->path('src');
```


<a name="options-ignore"></a>
### `ignore()`
CLI: `--ignore`

Ignore one or more directory or file paths.

```php
mutate()
    ->ignore('src/Contracts');
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

Limit mutations to code that has uncommitted changes.

```php
mutate()
    ->uncommittedOnly();
```


<a name="options-changed-only"></a>
### `changedOnly()`
CLI: `--changed-only`

Limit mutations to code that has changed relative to a common ancestor of the given branch (defaults to `main`).

```php
mutate()
    ->changedOnly(); // or ->changedOnly('add-xyz');
```


<a name="options-stop-on-escaped"></a>
### `stopOnEscaped()`
CLI: `--stop-on-escaped`

Stop execution upon first escaped mutant.

```php
mutate()
    ->stopOnEscaped();
```


<a name="options-stop-on-not-covered"></a>
### `stopOnNotCovered()`
CLI: `--stop-on-not-covered`

Stop execution upon first not covered mutant.

```php
mutate()
    ->stopOnNotCovered();
```


<a name="options-bail"></a>
### `bail()`
CLI: `--bail`

Stop execution upon first not covered or escaped mutant.

```php
mutate()
    ->bail();
```


<a name="options-retry"></a>
### `retry()`
CLI: `--retry`

If a mutation previously escaped, you typically want to run them first. In such cases, you can use the `--retry` option.

The `--retry` flag reorders your mutations by prioritizing the previously escaped mutations. If there were no past escaped mutations, the suite runs as usual.

Additionally, it will stop execution upon first escaped mutant.

```php
mutate()
    ->retry();
```


<a name="options-min"></a>
### `min()`
CLI: `--min`

Enforce a minimum mutation score threshold. For more information see [Minimum Score Threshold Enforcement](#minimum-score-threshold-enforcement).

```php
mutate()
    ->min(100);
```

You can pass an optional second parameter to ignore the minimum score threshold if zero mutations are generated. In this case Pest will exit with code 0.

```php
mutate()
    ->min(100, failOnZeroMutations: false);
```


<a name="options-ignore-min-score-on-zero-mutations"></a>
### `ignoreMinScoreOnZeroMutations()`
CLI: `--ignore-min-score-on-zero-mutations`

Ignores the minimum score threshold if zero mutations are generated. In this case Pest will exit with code 0.

```php
mutate()
    ->ignoreMinScoreOnZeroMutations();
```


<a name="options-id"></a>
### `--id`

Run only the mutation with the given ID. You can find the ID of a mutation in the console output of a previous run.

```bash
vendor/bin/pest --mutate --id=fa6913f68aa87747
```


<a name="options-no-cache"></a>
### `--no-cache`

Disables the cache (This option is only available on the cli).

```bash
vendor/bin/pest --mutate --no-cache
```


<a name="options-clear-cache"></a>
### `--clear-cache`

Clears the cache (This option is only available on the cli).

```bash
vendor/bin/pest --mutate --clear-cache
```


## Performance

Mutation testing is potentially very time-consuming and resource intensive because of the sheer amount of possible mutations and tests to run them against.

Therefore, Pest Mutation Testing is optimized to limit the amount of mutations and tests to run against as much as possible. To achieve this, it uses the following strategies:
- Limit the amount of possible mutations by having a carefully chosen set of mutators
- Run only tests that covers the mutated code
- It tries to reuse cached mutations
- Run mutations in a reasonable order
- Provide options to stop on first escaped or not covered mutation

But there is much more you can do to improve performance. Especially if you have a larger code base and/or you are using mutations testing while developing locally.

### Use a code coverage driver

If you have a code coverage driver available, Pest will use it to only run tests that cover the mutated code.

**Supports [XDebug 3.0+](https://xdebug.org/docs/install/)** or [PCOV](https://github.com/krakjoe/pcov).

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

Run tests against multiple mutations in parallel. This can significantly reduce the time it takes to run mutation tests.

Against a single mutation the tests are not run in parallel, regardless of the parallel option.

```bash
vendor/bin/pest --mutate --parallel
```

### Reduce the number of mutators

Reduce the number of mutations by choosing a smaller set of mutators.

```php
vendor/bin/pest --mutate --mutator=ArithmeticPlusToMinus
```

### Profiling

You can profile the performance of the mutations by using the `--profile` option.
It outputs a list of the ten slowest mutations.

```bash
vendor/bin/pest --mutate --profile
```

## Ignoring Mutations

### Ignore for a single line

Sometimes, you may want to prevent a line from being mutated. To do so, you may use the `@pest-mutate-ignore` annotation:

```php
if($age >= 18) // @pest-mutate-ignore
    // ...
];
```

If you want to ignore only a specific mutator, you can add a comma separated list of mutator names:

```php
if($age >= 18) // @pest-mutate-ignore: GreaterOrEqualToGreater
    // ...
];
```

### Ignore for multiple lines

To ignore mutations on large parts of the code you can add the annotation to a class, method or statement to ignore all mutations within the elements scope.

To ignore only specific mutators, you can add a comma separated list of mutator names: `@pest-mutate-ignore: GreaterOrEqualToGreater,IfNegated`

#### Class level
```php
/**
 * @pest-mutate-ignore
 */
class Test {
    // ...
}
```

#### Method or function level
```php
/**
 * @pest-mutate-ignore
 */
public function test() {
    // ...
}
```

#### Statement level
```php
/** @pest-mutate-ignore */
for($i = 0; $i < 10; $i++) {
    // ...
}
```

## Minimum Score Threshold Enforcement

Just like code coverage, mutation coverage can also be enforced. You can use the `--mutate` and `--min` options to define the minimum threshold value for the mutation score. If the specified threshold is not met, Pest will report a failure.

```bash
./vendor/bin/pest --mutate --min=100
```

If zero mutations are generated, the score is considered to be 0 and Pest will report a failure. You can use the `--ignore-min-score-on-zero-mutations` option to ignore the minimum score threshold if zero mutations are generated. In this case Pest will exit with code 0.

```bash
./vendor/bin/pest --mutate --min=100 --ignore-min-score-on-zero-mutations
```

## Custom Mutators

> WIP: Custom mutators are not implemented yet!

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
