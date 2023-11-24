This repository contains the Pest Plugin Mutate.

> **This plugin hasn't been officially released yet and is currently in an early development stage.**
> 
> Some of the known bugs and missing features are listed in the WIP.md file. 

To stay up to date, follow me on social media (**[Twitter/X](https://twitter.com/gehrisandro)**, **[Mastodon](https://phpc.social/@gehrisandro)**, **[Bluesky](https://bsky.app/profile/gehrisandro.bsky.social)**) or keep an eye on this repository.

---

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

> Attention: This requires a change to the Pest core, which is not merged yet. You need to install [this fork](https://github.com/gehrisandro/pest/tree/mutate) of Pest in order to use `->mutate()` directly on your test cases or describe blocks.
> All other features are not affected and do work with the official Pest 2.x version.

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
- [`ignore()`](#ignore)
- [`class()`](#class)
- [`mutator()`](#mutator)
- [`except()`](#except)
- [`coveredOnly()`](#coveredOnly)
- [`uncommittedOnly()`](#uncommittedOnly)
- [`changedOnly()`](#changedOnly)
- [`stopOnSurvived()`](#stopOnSurvived)
- [`stopOnNotCovered()`](#stopOnNotCovered)
- [`bail()`](#bail)

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


<a name="options-stop-on-survived"></a>
### `stopOnSurvived()`
CLI: `--stop-on-survived`

Stop execution upon first survived mutant.

```php
mutate()
    ->stopOnSurvived();
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

Stop execution upon first not covered or survived mutant.

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
- Run mutations in a reasonable order
- Provide options to stop on first survived or not covered mutation

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

Run tests against multiple mutations in parallel. This can significantly reduce the time it takes to run mutation tests.

Against a single mutation the tests are not run in parallel, regardless of the parallel option.

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

## Minimum Score Threshold Enforcement

Just like code coverage, mutation coverage can also be enforced. You can use the `--mutate` and `--min` options to define the minimum threshold value for the mutation score. If the specified threshold is not met, Pest will report a failure.

```bash
./vendor/bin/pest --mutate --min=100
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


# Mutator Reference

A comprehensive list of all mutators available.

## Sets

The mutators are grouped in sets.

### Default

A set of mutators that are enabled by default. Which keeps a good balance between performance and mutation coverage.

This set consists of various mutators from different sets. Mutators included are indicated with a asterisk (*).

### ArithmeticSet

<div class="collection-method-list" markdown="1">

- [BitwiseAndToBitwiseOr](#bitwiseandtobitwiseor-) (*)
- [BitwiseOrToBitwiseAnd](#bitwiseortobitwiseand-) (*)
- [BitwiseXorToBitwiseAnd](#bitwisexortobitwiseand-) (*)
- [PlusToMinus](#plustominus-) (*)
- [MinusToPlus](#minustoplus-) (*)
- [DivisionToMultiplication](#divisiontomultiplication-) (*)
- [MultiplicationToDivision](#multiplicationtodivision-) (*)
- [ModulusToMultiplication](#modulustomultiplication-) (*)
- [PowerToMultiplication](#powertomultiplication-) (*)
- [ShiftLeftToShiftRight](#shiftlefttoshiftright-) (*)
- [ShiftRightToShiftLeft](#shiftrighttoshiftleft-) (*)
- [PostDecrementToPostIncrement](#postdecrementtopostincrement-) (*)
- [PostIncrementToPostDecrement](#postincrementtopostdecrement-) (*)
- [PreDecrementToPreIncrement](#predecrementtopreincrement-) (*)
- [PreIncrementToPreDecrement](#preincrementtopredecrement-) (*)

</div>

### ArraySet

<div class="collection-method-list" markdown="1">

- [ArrayKeyFirstToArrayKeyLast](#arraykeyfirsttoarraykeylast-) (*)
- [ArrayKeyLastToArrayKeyFirst](#arraykeylasttoarraykeyfirst-) (*)
- [ArrayPopToArrayShift](#arraypoptoarrayshift-) (*)
- [ArrayShiftToArrayPop](#arrayshifttoarraypop-) (*)
- [UnwrapArrayChangeKeyCase](#unwraparraychangekeycase-) (*)
- [UnwrapArrayChunk](#unwraparraychunk-) (*)
- [UnwrapArrayColumn](#unwraparraycolumn-) (*)
- [UnwrapArrayCombine](#unwraparraycombine-) (*)
- [UnwrapArrayCountValues](#unwraparraycountvalues-) (*)
- [UnwrapArrayDiffAssoc](#unwraparraydiffassoc-) (*)
- [UnwrapArrayDiffKey](#unwraparraydiffkey-) (*)
- [UnwrapArrayDiffUassoc](#unwraparraydiffuassoc-) (*)
- [UnwrapArrayDiffUkey](#unwraparraydiffukey-) (*)
- [UnwrapArrayDiff](#unwraparraydiff-) (*)
- [UnwrapArrayFilter](#unwraparrayfilter-) (*)
- [UnwrapArrayFlip](#unwraparrayflip-) (*)
- [UnwrapArrayIntersectAssoc](#unwraparrayintersectassoc-) (*)
- [UnwrapArrayIntersectKey](#unwraparrayintersectkey-) (*)
- [UnwrapArrayIntersectUassoc](#unwraparrayintersectuassoc-) (*)
- [UnwrapArrayIntersectUkey](#unwraparrayintersectukey-) (*)
- [UnwrapArrayIntersect](#unwraparrayintersect-) (*)
- [UnwrapArrayKeys](#unwraparraykeys-) (*)
- [UnwrapArrayMap](#unwraparraymap-) (*)
- [UnwrapArrayMergeRecursive](#unwraparraymergerecursive-) (*)
- [UnwrapArrayMerge](#unwraparraymerge-) (*)
- [UnwrapArrayPad](#unwraparraypad-) (*)
- [UnwrapArrayReduce](#unwraparrayreduce-) (*)
- [UnwrapArrayReplaceRecursive](#unwraparrayreplacerecursive-) (*)
- [UnwrapArrayReplace](#unwraparrayreplace-) (*)
- [UnwrapArrayReverse](#unwraparrayreverse-) (*)
- [UnwrapArraySlice](#unwraparrayslice-) (*)
- [UnwrapArraySplice](#unwraparraysplice-) (*)
- [UnwrapArrayUdiffAssoc](#unwraparrayudiffassoc-) (*)
- [UnwrapArrayUdiffUassoc](#unwraparrayudiffuassoc-) (*)
- [UnwrapArrayUdiff](#unwraparrayudiff-) (*)
- [UnwrapArrayUintersectAssoc](#unwraparrayuintersectassoc-) (*)
- [UnwrapArrayUintersectUassoc](#unwraparrayuintersectuassoc-) (*)
- [UnwrapArrayUintersect](#unwraparrayuintersect-) (*)
- [UnwrapArrayUnique](#unwraparrayunique-) (*)
- [UnwrapArrayValues](#unwraparrayvalues-) (*)

</div>

### AssignmentSet

<div class="collection-method-list" markdown="1">

- [BitwiseAndToBitwiseOr](#bitwiseandtobitwiseor-) (*)
- [BitwiseOrToBitwiseAnd](#bitwiseortobitwiseand-) (*)
- [BitwiseXorToBitwiseAnd](#bitwisexortobitwiseand-) (*)
- [CoalesceEqualToEqual](#coalesceequaltoequal-) (*)
- [ConcatEqualToEqual](#concatequaltoequal-) (*)
- [DivideEqualToMultiplyEqual](#divideequaltomultiplyequal-) (*)
- [MinusEqualToPlusEqual](#minusequaltoplusequal-) (*)
- [ModulusEqualToMultiplyEqual](#modulusequaltomultiplyequal-) (*)
- [MultiplyEqualToDivideEqual](#multiplyequaltodivideequal-) (*)
- [PlusEqualToMinusEqual](#plusequaltominusequal-) (*)
- [PowerEqualToMultiplyEqual](#powerequaltomultiplyequal-) (*)
- [ShiftLeftToShiftRight](#shiftlefttoshiftright-) (*)
- [ShiftRightToShiftLeft](#shiftrighttoshiftleft-) (*)

</div>

### CastingSet

<div class="collection-method-list" markdown="1">

- [RemoveArrayCast](#removearraycast-) (*)
- [RemoveBooleanCast](#removebooleancast-) (*)
- [RemoveDoubleCast](#removedoublecast-) (*)
- [RemoveIntegerCast](#removeintegercast-) (*)
- [RemoveObjectCast](#removeobjectcast-) (*)
- [RemoveStringCast](#removestringcast-) (*)

</div>

### ControlStructuresSet

<div class="collection-method-list" markdown="1">

- [IfNegated](#ifnegated-) (*)
- [ElseIfNegated](#elseifnegated-) (*)
- [TernaryNegated](#ternarynegated-) (*)
- [ForAlwaysFalse](#foralwaysfalse-) (*)
- [ForeachEmptyIterable](#foreachemptyiterable-) (*)
- [DoWhileAlwaysFalse](#dowhilealwaysfalse-) (*)
- [WhileAlwaysFalse](#whilealwaysfalse-) (*)
- [BreakToContinue](#breaktocontinue-) (*)
- [ContinueToBreak](#continuetobreak-) (*)

</div>

### EqualitySet

<div class="collection-method-list" markdown="1">

- [EqualToNotEqual](#equaltonotequal-) (*)
- [NotEqualToEqual](#notequaltoequal-) (*)
- [IdenticalToNotIdentical](#identicaltonotidentical-) (*)
- [NotIdenticalToIdentical](#notidenticaltoidentical-) (*)
- [GreaterToGreaterOrEqual](#greatertogreaterorequal-) (*)
- [GreaterToSmallerOrEqual](#greatertosmallerorequal-) (*)
- [GreaterOrEqualToGreater](#greaterorequaltogreater-) (*)
- [GreaterOrEqualToSmaller](#greaterorequaltosmaller-) (*)
- [SmallerToGreaterOrEqual](#smallertogreaterorequal-) (*)
- [SmallerToSmallerOrEqual](#smallertosmallerorequal-) (*)
- [SmallerOrEqualToGreater](#smallerorequaltogreater-) (*)
- [SmallerOrEqualToSmaller](#smallerorequaltosmaller-) (*)
- [EqualToIdentical](#equaltoidentical-) (*)
- [NotEqualToNotIdentical](#notequaltonotidentical-) (*)
- [SpaceshipSwitchSides](#spaceshipswitchsides-) (*)
- [IdenticalToEqual](#identicaltoequal)
- [NotIdenticalToNotEqual](#notidenticaltonotequal)

</div>

### LogicalSet

<div class="collection-method-list" markdown="1">

- [BooleanAndToBooleanOr](#booleanandtobooleanor-) (*)
- [BooleanOrToBooleanAnd](#booleanortobooleanand-) (*)
- [CoalesceRemoveLeft](#coalesceremoveleft-) (*)
- [LogicalAndToLogicalOr](#logicalandtologicalor-) (*)
- [LogicalOrToLogicalAnd](#logicalortologicaland-) (*)
- [LogicalXorToLogicalAnd](#logicalxortologicaland-) (*)
- [FalseToTrue](#falsetotrue-) (*)
- [TrueToFalse](#truetofalse-) (*)
- [InstanceOfToTrue](#instanceoftotrue-) (*)
- [InstanceOfToFalse](#instanceoftofalse-) (*)
- [RemoveNot](#removenot-) (*)

</div>

### MathSet

<div class="collection-method-list" markdown="1">

- [MinToMax](#mintomax-) (*)
- [MaxToMin](#maxtomin-) (*)
- [RoundToFloor](#roundtofloor-) (*)
- [RoundToCeil](#roundtoceil-) (*)
- [FloorToRound](#floortoround-) (*)
- [FloorToCiel](#floortociel-) (*)
- [CeilToFloor](#ceiltofloor-) (*)
- [CeilToRound](#ceiltoround-) (*)

</div>

### NumberSet

<div class="collection-method-list" markdown="1">

- [DecrementFloat](#decrementfloat-) (*)
- [IncrementFloat](#incrementfloat-) (*)
- [DecrementInteger](#decrementinteger-) (*)
- [IncrementInteger](#incrementinteger-) (*)

</div>

### RemovalSet

<div class="collection-method-list" markdown="1">

- [RemoveArrayItem](#removearrayitem-) (*)
- [RemoveFunctionCall](#removefunctioncall-) (*)
- [RemoveMethodCall](#removemethodcall-) (*)
- [RemoveNullSafeOperator](#removenullsafeoperator-) (*)

</div>

### ReturnSet

<div class="collection-method-list" markdown="1">

- [AlwaysReturnNull](#alwaysreturnnull-) (*)
- [AlwaysReturnEmptyArray](#alwaysreturnemptyarray-) (*)

</div>

### StringSet

<div class="collection-method-list" markdown="1">

- [ConcatRemoveLeft](#concatremoveleft-) (*)
- [ConcatRemoveRight](#concatremoveright-) (*)
- [ConcatSwitchSides](#concatswitchsides-) (*)
- [EmptyStringToNotEmpty](#emptystringtonotempty-) (*)
- [NotEmptyStringToEmpty](#notemptystringtoempty-) (*)
- [StrStartsWithToStrEndsWith](#strstartswithtostrendswith-) (*)
- [StrEndsWithToStrStartsWith](#strendswithtostrstartswith-) (*)
- [UnwrapChop](#unwrapchop-) (*)
- [UnwrapChunkSplit](#unwrapchunksplit-) (*)
- [UnwrapHtmlentities](#unwraphtmlentities-) (*)
- [UnwrapHtmlEntityDecode](#unwraphtmlentitydecode-) (*)
- [UnwrapHtmlspecialchars](#unwraphtmlspecialchars-) (*)
- [UnwrapHtmlspecialcharsDecode](#unwraphtmlspecialcharsdecode-) (*)
- [UnwrapLcfirst](#unwraplcfirst-) (*)
- [UnwrapLtrim](#unwrapltrim-) (*)
- [UnwrapMd5](#unwrapmd5-) (*)
- [UnwrapNl2br](#unwrapnl2br-) (*)
- [UnwrapRtrim](#unwraprtrim-) (*)
- [UnwrapStripTags](#unwrapstriptags-) (*)
- [UnwrapStrIreplace](#unwrapstrireplace-) (*)
- [UnwrapStrPad](#unwrapstrpad-) (*)
- [UnwrapStrRepeat](#unwrapstrrepeat-) (*)
- [UnwrapStrReplace](#unwrapstrreplace-) (*)
- [UnwrapStrrev](#unwrapstrrev-) (*)
- [UnwrapStrShuffle](#unwrapstrshuffle-) (*)
- [UnwrapStrtolower](#unwrapstrtolower-) (*)
- [UnwrapStrtoupper](#unwrapstrtoupper-) (*)
- [UnwrapSubstr](#unwrapsubstr-) (*)
- [UnwrapTrim](#unwraptrim-) (*)
- [UnwrapUcfirst](#unwrapucfirst-) (*)
- [UnwrapUcwords](#unwrapucwords-) (*)
- [UnwrapWordwrap](#unwrapwordwrap-) (*)

</div>

### VisibilitySet

<div class="collection-method-list" markdown="1">

- [ConstantPublicToProtected](#constantpublictoprotected)
- [ConstantProtectedToPrivate](#constantprotectedtoprivate)
- [FunctionPublicToProtected](#functionpublictoprotected-) (*)
- [FunctionProtectedToPrivate](#functionprotectedtoprivate)
- [PropertyPublicToProtected](#propertypublictoprotected)
- [PropertyProtectedToPrivate](#propertyprotectedtoprivate)

</div>

### LaravelSet

<div class="collection-method-list" markdown="1">

- [LaravelRemoveStringableUpper](#laravelremovestringableupper)
- [LaravelUnwrapStrUpper](#laravelunwrapstrupper)

</div>

## Mutators

An alphabetical list of all mutators.

<a name="alwaysreturnemptyarray"></a>
### AlwaysReturnEmptyArray (*)
Set: Return

Mutates a return statement to an empty array

```php
return [1];  // [tl! remove]
return [];  // [tl! add]
```

<a name="alwaysreturnnull"></a>
### AlwaysReturnNull (*)
Set: Return

Mutates a return statement to null if it is not null

```php
return $a;  // [tl! remove]
return null;  // [tl! add]
```

<a name="arraykeyfirsttoarraykeylast"></a>
### ArrayKeyFirstToArrayKeyLast (*)
Set: Array

Replaces `array_key_first` with `array_key_last`.

```php
$a = array_key_first([1, 2, 3]);  // [tl! remove]
$a = array_key_last([1, 2, 3]);  // [tl! add]
```

<a name="arraykeylasttoarraykeyfirst"></a>
### ArrayKeyLastToArrayKeyFirst (*)
Set: Array

Replaces `array_key_last` with `array_key_first`.

```php
$a = array_key_last([1, 2, 3]);  // [tl! remove]
$a = array_key_first([1, 2, 3]);  // [tl! add]
```

<a name="arraypoptoarrayshift"></a>
### ArrayPopToArrayShift (*)
Set: Array

Replaces `array_pop` with `array_shift`.

```php
$a = array_pop([1, 2, 3]);  // [tl! remove]
$a = array_shift([1, 2, 3]);  // [tl! add]
```

<a name="arrayshifttoarraypop"></a>
### ArrayShiftToArrayPop (*)
Set: Array

Replaces `array_shift` with `array_pop`.

```php
$a = array_shift([1, 2, 3]);  // [tl! remove]
$a = array_pop([1, 2, 3]);  // [tl! add]
```

<a name="bitwiseandtobitwiseor"></a>
### BitwiseAndToBitwiseOr (*)
Set: Arithmetic

Replaces `&` with `|`.

```php
$c = $a & $b;  // [tl! remove]
$c = $a | $b;  // [tl! add]
```

<a name="bitwiseandtobitwiseor"></a>
### BitwiseAndToBitwiseOr (*)
Set: Assignment

Replaces `&=` with `|=`.

```php
$a &= $b;  // [tl! remove]
$a |= $b;  // [tl! add]
```

<a name="bitwiseortobitwiseand"></a>
### BitwiseOrToBitwiseAnd (*)
Set: Arithmetic

Replaces `|` with `&`.

```php
$c = $a | $b;  // [tl! remove]
$c = $a & $b;  // [tl! add]
```

<a name="bitwiseortobitwiseand"></a>
### BitwiseOrToBitwiseAnd (*)
Set: Assignment

Replaces `|=` with `&=`.

```php
$a |= $b;  // [tl! remove]
$a &= $b;  // [tl! add]
```

<a name="bitwisexortobitwiseand"></a>
### BitwiseXorToBitwiseAnd (*)
Set: Arithmetic

Replaces `^` with `&`.

```php
$c = $a ^ $b;  // [tl! remove]
$c = $a & $b;  // [tl! add]
```

<a name="bitwisexortobitwiseand"></a>
### BitwiseXorToBitwiseAnd (*)
Set: Assignment

Replaces `^=` with `&=`.

```php
$a ^= $b;  // [tl! remove]
$a &= $b;  // [tl! add]
```

<a name="booleanandtobooleanor"></a>
### BooleanAndToBooleanOr (*)
Set: Logical

Converts the boolean and operator to the boolean or operator.

```php
if ($a && $b) {  // [tl! remove]
if ($a || $b) {  // [tl! add]
    // ...
}
```

<a name="booleanortobooleanand"></a>
### BooleanOrToBooleanAnd (*)
Set: Logical

Converts the boolean or operator to the boolean and operator.

```php
if ($a || $b) {  // [tl! remove]
if ($a && $b) {  // [tl! add]
    // ...
}
```

<a name="breaktocontinue"></a>
### BreakToContinue (*)
Set: ControlStructures

Replaces `break` with `continue`.

```php
foreach ($items as $item) {
    if ($item === 'foo') {
        break;  // [tl! remove]
        continue;  // [tl! add]
    }
}
```

<a name="ceiltofloor"></a>
### CeilToFloor (*)
Set: Math

Replaces `ceil` function with `floor` function.

```php
$a = ceil(1.2);  // [tl! remove]
$a = floor(1.2);  // [tl! add]
```

<a name="ceiltoround"></a>
### CeilToRound (*)
Set: Math

Replaces `ceil` function with `round` function.

```php
$a = ceil(1.2);  // [tl! remove]
$a = round(1.2);  // [tl! add]
```

<a name="coalesceequaltoequal"></a>
### CoalesceEqualToEqual (*)
Set: Assignment

Replaces `??=` with `=`.

```php
$a ??= $b;  // [tl! remove]
$a = $b;  // [tl! add]
```

<a name="coalesceremoveleft"></a>
### CoalesceRemoveLeft (*)
Set: Logical

Removes the left side of the coalesce operator.

```php
return $a ?? $b;  // [tl! remove]
return $b;  // [tl! add]
```

<a name="concatequaltoequal"></a>
### ConcatEqualToEqual (*)
Set: Assignment

Replaces `.=` with `=`.

```php
$a .= $b;  // [tl! remove]
$a = $b;  // [tl! add]
```

<a name="concatremoveleft"></a>
### ConcatRemoveLeft (*)
Set: String

Removes the left part of a concat expression.

```php
$a = 'Hello' . ' World';  // [tl! remove]
$a = ' World';  // [tl! add]
```

<a name="concatremoveright"></a>
### ConcatRemoveRight (*)
Set: String

Removes the right part of a concat expression.

```php
$a = 'Hello' . ' World';  // [tl! remove]
$a = 'Hello';  // [tl! add]
```

<a name="concatswitchsides"></a>
### ConcatSwitchSides (*)
Set: String

Switches the sides of a concat expression.

```php
$a = 'Hello' . ' World';  // [tl! remove]
$a = ' World' . 'Hello';  // [tl! add]
```

<a name="constantprotectedtoprivate"></a>
### ConstantProtectedToPrivate
Set: Visibility

Mutates a protected constant to a private constant

```php
protected const FOO = true;  // [tl! remove]
private const FOO = true;  // [tl! add]
```

<a name="constantpublictoprotected"></a>
### ConstantPublicToProtected
Set: Visibility

Mutates a public constant to a protected constant

```php
public const FOO = true;  // [tl! remove]
protected const FOO = true;  // [tl! add]
```

<a name="continuetobreak"></a>
### ContinueToBreak (*)
Set: ControlStructures

Replaces `continue` with `break`.

```php
foreach ($items as $item) {
    if ($item === 'foo') {
        continue;  // [tl! remove]
        break;  // [tl! add]
    }
}
```

<a name="decrementfloat"></a>
### DecrementFloat (*)
Set: Number

Decrements a float number by 1.

```php
$a = 1.2;  // [tl! remove]
$a = 0.2;  // [tl! add]
```

<a name="decrementinteger"></a>
### DecrementInteger (*)
Set: Number

Decrements an integer number by 1.

```php
$a = 1;  // [tl! remove]
$a = 0;  // [tl! add]
```

<a name="divideequaltomultiplyequal"></a>
### DivideEqualToMultiplyEqual (*)
Set: Assignment

Replaces `/=` with `*=`.

```php
$a /= $b;  // [tl! remove]
$a *= $b;  // [tl! add]
```

<a name="divisiontomultiplication"></a>
### DivisionToMultiplication (*)
Set: Arithmetic

Replaces `/` with `*`.

```php
$c = $a / $b;  // [tl! remove]
$c = $a * $b;  // [tl! add]
```

<a name="dowhilealwaysfalse"></a>
### DoWhileAlwaysFalse (*)
Set: ControlStructures

Makes the condition in a do-while loop always false.

```php
do {
    // ...
} while ($a < 100);  // [tl! remove]
} while (false);  // [tl! add]
```

<a name="elseifnegated"></a>
### ElseIfNegated (*)
Set: ControlStructures

Negates the condition in an elseif statement.

```php
if ($a === 1) {
    // ...
} elseif ($a === 2) {  // [tl! remove]
} elseif (!($a === 2)) {  // [tl! add]
    // ...
}
```

<a name="emptystringtonotempty"></a>
### EmptyStringToNotEmpty (*)
Set: String

Changes an empty string to a non-empty string.

```php
$a = '';  // [tl! remove]
$a = 'PEST Mutator was here!';  // [tl! add]
```

<a name="equaltoidentical"></a>
### EqualToIdentical (*)
Set: Equality

Converts the equality operator to the identical operator.

```php
if ($a == $b) {  // [tl! remove]
if ($a === $b) {  // [tl! add]
    // ...
}
```

<a name="equaltonotequal"></a>
### EqualToNotEqual (*)
Set: Equality

Converts the equality operator to the not equal operator.

```php
if ($a == $b) {  // [tl! remove]
if ($a != $b) {  // [tl! add]
    // ...
}
```

<a name="falsetotrue"></a>
### FalseToTrue (*)
Set: Logical

Converts `false` to `true`.

```php
if (false) {  // [tl! remove]
if (true) {  // [tl! add]
    // ...
}
```

<a name="floortociel"></a>
### FloorToCiel (*)
Set: Math

Replaces `floor` function with `ceil` function.

```php
$a = floor(1.2);  // [tl! remove]
$a = ceil(1.2);  // [tl! add]
```

<a name="floortoround"></a>
### FloorToRound (*)
Set: Math

Replaces `floor` function with `round` function.

```php
$a = floor(1.2);  // [tl! remove]
$a = round(1.2);  // [tl! add]
```

<a name="foralwaysfalse"></a>
### ForAlwaysFalse (*)
Set: ControlStructures

Makes the condition in a for loop always false.

```php
for ($i = 0; $i < 10; $i++) {  // [tl! remove]
for ($i = 0; false; $i++) {  // [tl! add]
    // ...
}
```

<a name="foreachemptyiterable"></a>
### ForeachEmptyIterable (*)
Set: ControlStructures

Replaces the iterable in a foreach loop with an empty array.

```php
foreach ($items as $item) {  // [tl! remove]
foreach ([] as $item) {  // [tl! add]
    // ...
}
```

<a name="functionprotectedtoprivate"></a>
### FunctionProtectedToPrivate
Set: Visibility

Mutates a protected function to a private function

```php
protected function foo(): bool  // [tl! remove]
private function foo(): bool  // [tl! add]
{
    return true;
}
```

<a name="functionpublictoprotected"></a>
### FunctionPublicToProtected (*)
Set: Visibility

Mutates a public function to a protected function

```php
public function foo(): bool  // [tl! remove]
protected function foo(): bool  // [tl! add]
{
    return true;
}
```

<a name="greaterorequaltogreater"></a>
### GreaterOrEqualToGreater (*)
Set: Equality

Converts the greater or equal operator to the greater operator.

```php
if ($a >= $b) {  // [tl! remove]
if ($a > $b) {  // [tl! add]
    // ...
}
```

<a name="greaterorequaltosmaller"></a>
### GreaterOrEqualToSmaller (*)
Set: Equality

Converts the greater or equal operator to the smaller operator.

```php
if ($a >= $b) {  // [tl! remove]
if ($a < $b) {  // [tl! add]
    // ...
}
```

<a name="greatertogreaterorequal"></a>
### GreaterToGreaterOrEqual (*)
Set: Equality

Converts the greater operator to the greater or equal operator.

```php
if ($a > $b) {  // [tl! remove]
if ($a >= $b) {  // [tl! add]
    // ...
}
```

<a name="greatertosmallerorequal"></a>
### GreaterToSmallerOrEqual (*)
Set: Equality

Converts the greater operator to the smaller or equal operator.

```php
if ($a > $b) {  // [tl! remove]
if ($a <= $b) {  // [tl! add]
    // ...
}
```

<a name="identicaltoequal"></a>
### IdenticalToEqual
Set: Equality

Converts the identical operator to the equality operator.

```php
if ($a === $b) {  // [tl! remove]
if ($a == $b) {  // [tl! add]
    // ...
}
```

<a name="identicaltonotidentical"></a>
### IdenticalToNotIdentical (*)
Set: Equality

Converts the identical operator to the not identical operator.

```php
if ($a === $b) {  // [tl! remove]
if ($a !== $b) {  // [tl! add]
    // ...
}
```

<a name="ifnegated"></a>
### IfNegated (*)
Set: ControlStructures

Negates the condition in an if statement.

```php
if ($a === 1) {  // [tl! remove]
if (!($a === 1)) {  // [tl! add]
    // ...
}
```

<a name="incrementfloat"></a>
### IncrementFloat (*)
Set: Number

Increments a float number by 1.

```php
$a = 1.2;  // [tl! remove]
$a = 2.2;  // [tl! add]
```

<a name="incrementinteger"></a>
### IncrementInteger (*)
Set: Number

Increments an integer number by 1.

```php
$a = 1;  // [tl! remove]
$a = 2;  // [tl! add]
```

<a name="instanceoftofalse"></a>
### InstanceOfToFalse (*)
Set: Logical

Converts `instanceof` to `false`.

```php
if ($a instanceof $b) {  // [tl! remove]
if (false) {  // [tl! add]
    // ...
}
```

<a name="instanceoftotrue"></a>
### InstanceOfToTrue (*)
Set: Logical

Converts `instanceof` to `true`.

```php
if ($a instanceof $b) {  // [tl! remove]
if (true) {  // [tl! add]
    // ...
}
```

<a name="laravelremovestringableupper"></a>
### LaravelRemoveStringableUpper
Set: Laravel

Removes the upper method call from a stringable object.

```php
Str::of('hello')->upper();  // [tl! remove]
Str::of('hello');  // [tl! add]
```

<a name="laravelunwrapstrupper"></a>
### LaravelUnwrapStrUpper
Set: Laravel

Unwraps the string upper method call.

```php
$a = Illuminate\Support\Str::upper('foo');  // [tl! remove]
$a = 'foo';  // [tl! add]
```

<a name="logicalandtologicalor"></a>
### LogicalAndToLogicalOr (*)
Set: Logical

Converts the logical and operator to the logical or operator.

```php
if ($a && $b) {  // [tl! remove]
if ($a || $b) {  // [tl! add]
    // ...
}
```

<a name="logicalortologicaland"></a>
### LogicalOrToLogicalAnd (*)
Set: Logical

Converts the logical or operator to the logical and operator.

```php
if ($a || $b) {  // [tl! remove]
if ($a && $b) {  // [tl! add]
    // ...
}
```

<a name="logicalxortologicaland"></a>
### LogicalXorToLogicalAnd (*)
Set: Logical

Converts the logical xor operator to the logical and operator.

```php
if ($a xor $b) {  // [tl! remove]
if ($a && $b) {  // [tl! add]
    // ...
}
```

<a name="maxtomin"></a>
### MaxToMin (*)
Set: Math

Replaces `max` function with `min` function.

```php
$a = max(1, 2);  // [tl! remove]
$a = min(1, 2);  // [tl! add]
```

<a name="mintomax"></a>
### MinToMax (*)
Set: Math

Replaces `min` function with `max` function.

```php
$a = min(1, 2);  // [tl! remove]
$a = max(1, 2);  // [tl! add]
```

<a name="minusequaltoplusequal"></a>
### MinusEqualToPlusEqual (*)
Set: Assignment

Replaces `-=` with `+=`.

```php
$a -= $b;  // [tl! remove]
$a += $b;  // [tl! add]
```

<a name="minustoplus"></a>
### MinusToPlus (*)
Set: Arithmetic

Replaces `-` with `+`.

```php
$c = $a - $b;  // [tl! remove]
$c = $a + $b;  // [tl! add]
```

<a name="modulusequaltomultiplyequal"></a>
### ModulusEqualToMultiplyEqual (*)
Set: Assignment

Replaces `%=` with `*=`.

```php
$a %= $b;  // [tl! remove]
$a *= $b;  // [tl! add]
```

<a name="modulustomultiplication"></a>
### ModulusToMultiplication (*)
Set: Arithmetic

Replaces `%` with `*`.

```php
$c = $a % $b;  // [tl! remove]
$c = $a * $b;  // [tl! add]
```

<a name="multiplicationtodivision"></a>
### MultiplicationToDivision (*)
Set: Arithmetic

Replaces `*` with `/`.

```php
$c = $a * $b;  // [tl! remove]
$c = $a / $b;  // [tl! add]
```

<a name="multiplyequaltodivideequal"></a>
### MultiplyEqualToDivideEqual (*)
Set: Assignment

Replaces `*=` with `/=`.

```php
$a *= $b;  // [tl! remove]
$a /= $b;  // [tl! add]
```

<a name="notemptystringtoempty"></a>
### NotEmptyStringToEmpty (*)
Set: String

Changes a non-empty string to an empty string.

```php
$a = 'Hello World';  // [tl! remove]
$a = '';  // [tl! add]
```

<a name="notequaltoequal"></a>
### NotEqualToEqual (*)
Set: Equality

Converts the not equal operator to the equal operator.

```php
if ($a != $b) {  // [tl! remove]
if ($a == $b) {  // [tl! add]
    // ...
}
```

<a name="notequaltonotidentical"></a>
### NotEqualToNotIdentical (*)
Set: Equality

Converts the not equal operator to the not identical operator.

```php
if ($a != $b) {  // [tl! remove]
if ($a !== $b) {  // [tl! add]
    // ...
}
```

<a name="notidenticaltoidentical"></a>
### NotIdenticalToIdentical (*)
Set: Equality

Converts the not identical operator to the identical operator.

```php
if ($a !== $b) {  // [tl! remove]
if ($a === $b) {  // [tl! add]
    // ...
}
```

<a name="notidenticaltonotequal"></a>
### NotIdenticalToNotEqual
Set: Equality

Converts the not identical operator to the not equal operator.

```php
if ($a !== $b) {  // [tl! remove]
if ($a != $b) {  // [tl! add]
    // ...
}
```

<a name="plusequaltominusequal"></a>
### PlusEqualToMinusEqual (*)
Set: Assignment

Replaces `+=` with `-=`.

```php
$a += $b;  // [tl! remove]
$a -= $b;  // [tl! add]
```

<a name="plustominus"></a>
### PlusToMinus (*)
Set: Arithmetic

Replaces `+` with `-`.

```php
$c = $a + $b;  // [tl! remove]
$c = $a - $b;  // [tl! add]
```

<a name="postdecrementtopostincrement"></a>
### PostDecrementToPostIncrement (*)
Set: Arithmetic

Replaces `--` with `++`.

```php
$b = $a--;  // [tl! remove]
$b = $a++;  // [tl! add]
```

<a name="postincrementtopostdecrement"></a>
### PostIncrementToPostDecrement (*)
Set: Arithmetic

Replaces `++` with `--`.

```php
$b = $a++;  // [tl! remove]
$b = $a--;  // [tl! add]
```

<a name="powerequaltomultiplyequal"></a>
### PowerEqualToMultiplyEqual (*)
Set: Assignment

Replaces `**=` with `*=`.

```php
$a **= $b;  // [tl! remove]
$a *= $b;  // [tl! add]
```

<a name="powertomultiplication"></a>
### PowerToMultiplication (*)
Set: Arithmetic

Replaces `**` with `*`.

```php
$c = $a ** $b;  // [tl! remove]
$c = $a * $b;  // [tl! add]
```

<a name="predecrementtopreincrement"></a>
### PreDecrementToPreIncrement (*)
Set: Arithmetic

Replaces `--` with `++`.

```php
$b = --$a;  // [tl! remove]
$b = ++$a;  // [tl! add]
```

<a name="preincrementtopredecrement"></a>
### PreIncrementToPreDecrement (*)
Set: Arithmetic

Replaces `++` with `--`.

```php
$b = ++$a;  // [tl! remove]
$b = --$a;  // [tl! add]
```

<a name="propertyprotectedtoprivate"></a>
### PropertyProtectedToPrivate
Set: Visibility

Mutates a protected property to a private property

```php
protected bool $foo = true;  // [tl! remove]
private bool $foo = true;  // [tl! add]
```

<a name="propertypublictoprotected"></a>
### PropertyPublicToProtected
Set: Visibility

Mutates a public property to a protected property

```php
public bool $foo = true;  // [tl! remove]
protected bool $foo = true;  // [tl! add]
```

<a name="removearraycast"></a>
### RemoveArrayCast (*)
Set: Casting

Removes array cast.

```php
$a = (array) $b;  // [tl! remove]
$a = $b;          // [tl! add]
```

<a name="removearrayitem"></a>
### RemoveArrayItem (*)
Set: Removal

Removes an item from an array

```php
return [
    'foo' => 1,  // [tl! remove]
    'bar' => 2,
];
```

<a name="removebooleancast"></a>
### RemoveBooleanCast (*)
Set: Casting

Removes boolean cast.

```php
$a = (bool) $b;  // [tl! remove]
$a = $b;         // [tl! add]
```

<a name="removedoublecast"></a>
### RemoveDoubleCast (*)
Set: Casting

Removes double cast.

```php
$a = (double) $b;  // [tl! remove]
$a = $b;           // [tl! add]
```

<a name="removefunctioncall"></a>
### RemoveFunctionCall (*)
Set: Removal

Removes a function call

```php
foo();  // [tl! remove]
```

<a name="removeintegercast"></a>
### RemoveIntegerCast (*)
Set: Casting

Removes integer cast.

```php
$a = (int) $b;  // [tl! remove]
$a = $b;        // [tl! add]
```

<a name="removemethodcall"></a>
### RemoveMethodCall (*)
Set: Removal

Removes a method call

```php
$this->foo();  // [tl! remove]
```

<a name="removenot"></a>
### RemoveNot (*)
Set: Logical

Removes the not operator.

```php
if (!$a) {  // [tl! remove]
if ($a) {  // [tl! add]
    // ...
}
```

<a name="removenullsafeoperator"></a>
### RemoveNullSafeOperator (*)
Set: Removal

Converts nullsafe method and property calls to regular calls.

```php
$a?->b();  // [tl! remove]
$a->b();  // [tl! add]
```

<a name="removeobjectcast"></a>
### RemoveObjectCast (*)
Set: Casting

Removes object cast.

```php
$a = (object) $b;  // [tl! remove]
$a = $b;           // [tl! add]
```

<a name="removestringcast"></a>
### RemoveStringCast (*)
Set: Casting

Removes string cast.

```php
$a = (string) $b;  // [tl! remove]
$a = $b;           // [tl! add]
```

<a name="roundtoceil"></a>
### RoundToCeil (*)
Set: Math

Replaces `round` function with `ceil` function.

```php
$a = round(1.2);  // [tl! remove]
$a = ceil(1.2);  // [tl! add]
```

<a name="roundtofloor"></a>
### RoundToFloor (*)
Set: Math

Replaces `round` function with `floor` function.

```php
$a = round(1.2);  // [tl! remove]
$a = floor(1.2);  // [tl! add]
```

<a name="shiftlefttoshiftright"></a>
### ShiftLeftToShiftRight (*)
Set: Arithmetic

Replaces `<<` with `>>`.

```php
$b = $a << 1;  // [tl! remove]
$b = $a >> 1;  // [tl! add]
```

<a name="shiftlefttoshiftright"></a>
### ShiftLeftToShiftRight (*)
Set: Assignment

Replaces `<<=` with `>>=`.

```php
$a <<= $b;  // [tl! remove]
$a >>= $b;  // [tl! add]
```

<a name="shiftrighttoshiftleft"></a>
### ShiftRightToShiftLeft (*)
Set: Arithmetic

Replaces `>>` with `<<`.

```php
$b = $a >> 1;  // [tl! remove]
$b = $a << 1;  // [tl! add]
```

<a name="shiftrighttoshiftleft"></a>
### ShiftRightToShiftLeft (*)
Set: Assignment

Replaces `>>=` with `<<=`.

```php
$a >>= $b;  // [tl! remove]
$a <<= $b;  // [tl! add]
```

<a name="smallerorequaltogreater"></a>
### SmallerOrEqualToGreater (*)
Set: Equality

Converts the smaller or equal operator to the greater operator.

```php
if ($a <= $b) {  // [tl! remove]
if ($a > $b) {  // [tl! add]
    // ...
}
```

<a name="smallerorequaltosmaller"></a>
### SmallerOrEqualToSmaller (*)
Set: Equality

Converts the smaller or equal operator to the smaller operator.

```php
if ($a <= $b) {  // [tl! remove]
if ($a < $b) {  // [tl! add]
    // ...
}
```

<a name="smallertogreaterorequal"></a>
### SmallerToGreaterOrEqual (*)
Set: Equality

Converts the smaller operator to the greater or equal operator.

```php
if ($a < $b) {  // [tl! remove]
if ($a >= $b) {  // [tl! add]
    // ...
}
```

<a name="smallertosmallerorequal"></a>
### SmallerToSmallerOrEqual (*)
Set: Equality

Converts the smaller operator to the smaller or equal operator.

```php
if ($a < $b) {  // [tl! remove]
if ($a <= $b) {  // [tl! add]
    // ...
}
```

<a name="spaceshipswitchsides"></a>
### SpaceshipSwitchSides (*)
Set: Equality

Switches the sides of the spaceship operator.

```php
return $a <=> $b;  // [tl! remove]
return $b <=> $a;  // [tl! add]
```

<a name="strendswithtostrstartswith"></a>
### StrEndsWithToStrStartsWith (*)
Set: String

Replaces `str_ends_with` with `str_starts_with`.

```php
$a = str_ends_with('Hello World', 'World');  // [tl! remove]
$a = str_starts_with('Hello World', 'World');  // [tl! add]
```

<a name="strstartswithtostrendswith"></a>
### StrStartsWithToStrEndsWith (*)
Set: String

Replaces `str_starts_with` with `str_ends_with`.

```php
$a = str_starts_with('Hello World', 'World');  // [tl! remove]
$a = str_ends_with('Hello World', 'World');  // [tl! add]
```

<a name="ternarynegated"></a>
### TernaryNegated (*)
Set: ControlStructures

Negates the condition in a ternary statement.

```php
$a = $b ? 1 : 2;  // [tl! remove]
$a = !$b ? 1 : 2;  // [tl! add]
```

<a name="truetofalse"></a>
### TrueToFalse (*)
Set: Logical

Converts `true` to `false`.

```php
if (true) {  // [tl! remove]
if (false) {  // [tl! add]
    // ...
}
```

<a name="unwraparraychangekeycase"></a>
### UnwrapArrayChangeKeyCase (*)
Set: Array

Unwraps `array_change_key_case` calls.

```php
$a = array_change_key_case(['foo' => 'bar'], CASE_UPPER);  // [tl! remove]
$a = ['foo' => 'bar'];  // [tl! add]
```

<a name="unwraparraychunk"></a>
### UnwrapArrayChunk (*)
Set: Array

Unwraps `array_chunk` calls.

```php
$a = array_chunk([1, 2, 3], 2);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraycolumn"></a>
### UnwrapArrayColumn (*)
Set: Array

Unwraps `array_column` calls.

```php
$a = array_column([['id' => 1], ['id' => 2]], 'id');  // [tl! remove]
$a = [['id' => 1], ['id' => 2]];  // [tl! add]
```

<a name="unwraparraycombine"></a>
### UnwrapArrayCombine (*)
Set: Array

Unwraps `array_combine` calls.

```php
$a = array_combine([1, 2, 3], [3, 4]);  // [tl! remove]
$a = [1, 2, 3]  // [tl! add]
```

<a name="unwraparraycountvalues"></a>
### UnwrapArrayCountValues (*)
Set: Array

Unwraps `array_count_values` calls.

```php
$a = array_count_values([1, 2, 3]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraydiff"></a>
### UnwrapArrayDiff (*)
Set: Array

Unwraps `array_diff` calls.

```php
$a = array_diff([1, 2, 3], [1, 2]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraydiffassoc"></a>
### UnwrapArrayDiffAssoc (*)
Set: Array

Unwraps `array_diff_assoc` calls.

```php
$a = array_diff_assoc(['foo' => 1, 'bar' => 2], ['foo' => 1])  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparraydiffkey"></a>
### UnwrapArrayDiffKey (*)
Set: Array

Unwraps `array_diff_key` calls.

```php
$a = array_diff_key(['foo' => 1, 'bar' => 2], ['foo' => 1]);  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparraydiffuassoc"></a>
### UnwrapArrayDiffUassoc (*)
Set: Array

Unwraps `array_diff_uassoc` calls.

```php
$a = array_diff_uassoc([1, 2, 3], [1, 2], 'strcmp');  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraydiffukey"></a>
### UnwrapArrayDiffUkey (*)
Set: Array

Unwraps `array_diff_ukey` calls.

```php
$a = array_diff_ukey(['foo' => 1, 'bar' => 2], ['foo' => 1], 'strcmp');  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparrayfilter"></a>
### UnwrapArrayFilter (*)
Set: Array

Unwraps `array_filter` calls.

```php
$a = array_filter([1, 2, 3], fn($value) => $value > 2);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayflip"></a>
### UnwrapArrayFlip (*)
Set: Array

Unwraps `array_flip` calls.

```php
$a = array_flip(['foo' => 1, 'bar' => 2]);  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparrayintersect"></a>
### UnwrapArrayIntersect (*)
Set: Array

Unwraps `array_intersect` calls.

```php
$a = array_intersect([1, 2, 3], [1, 2]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayintersectassoc"></a>
### UnwrapArrayIntersectAssoc (*)
Set: Array

Unwraps `array_intersect_assoc` calls.

```php
$a = array_intersect_assoc(['foo' => 1, 'bar' => 2], ['foo' => 1]);  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparrayintersectkey"></a>
### UnwrapArrayIntersectKey (*)
Set: Array

Unwraps `array_intersect_key` calls.

```php
$a = array_intersect_key(['foo' => 1, 'bar' => 2], ['foo' => 1]);  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparrayintersectuassoc"></a>
### UnwrapArrayIntersectUassoc (*)
Set: Array

Unwraps `array_intersect_uassoc` calls.

```php
$a = array_intersect_uassoc([1, 2, 3], [1, 2], 'strcmp');  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayintersectukey"></a>
### UnwrapArrayIntersectUkey (*)
Set: Array

Unwraps `array_intersect_ukey` calls.

```php
$a = array_intersect_ukey(['foo' => 1, 'bar' => 2], ['foo' => 1], 'strcmp');  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparraykeys"></a>
### UnwrapArrayKeys (*)
Set: Array

Unwraps `array_keys` calls.

```php
$a = array_keys(['foo' => 1, 'bar' => 2]);  // [tl! remove]
$a = ['foo' => 1, 'bar' => 2];  // [tl! add]
```

<a name="unwraparraymap"></a>
### UnwrapArrayMap (*)
Set: Array

Unwraps `array_map` calls.

```php
$a = array_map(fn ($value) => $value + 1, [1, 2, 3]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraymerge"></a>
### UnwrapArrayMerge (*)
Set: Array

Unwraps `array_merge` calls.

```php
$a = array_merge([1, 2, 3], [4, 5, 6]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraymergerecursive"></a>
### UnwrapArrayMergeRecursive (*)
Set: Array

Unwraps `array_merge_recursive` calls.

```php
$a = array_merge_recursive([1, 2, 3], [4, 5, 6]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraypad"></a>
### UnwrapArrayPad (*)
Set: Array

Unwraps `array_pad` calls.

```php
$a = array_pad([1, 2, 3], 5, 0);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayreduce"></a>
### UnwrapArrayReduce (*)
Set: Array

Unwraps `array_reduce` calls.

```php
$a = array_reduce([1, 2, 3], fn ($carry, $item) => $carry + $item, 0);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayreplace"></a>
### UnwrapArrayReplace (*)
Set: Array

Unwraps `array_replace` calls.

```php
$a = array_replace([1, 2, 3], ['a', 'b', 'c']);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayreplacerecursive"></a>
### UnwrapArrayReplaceRecursive (*)
Set: Array

Unwraps `array_replace_recursive` calls.

```php
$a = array_replace_recursive([1, 2, 3], ['a', 'b', 'c']);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayreverse"></a>
### UnwrapArrayReverse (*)
Set: Array

Unwraps `array_reverse` calls.

```php
$a = array_reverse([1, 2, 3]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayslice"></a>
### UnwrapArraySlice (*)
Set: Array

Unwraps `array_slice` calls.

```php
$a = array_slice([1, 2, 3], 1, 2);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparraysplice"></a>
### UnwrapArraySplice (*)
Set: Array

Unwraps `array_splice` calls.

```php
$a = array_splice([1, 2, 3], 0, 2, ['a', 'b']);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayudiff"></a>
### UnwrapArrayUdiff (*)
Set: Array

Unwraps `array_udiff` calls.

```php
$a = array_udiff([1, 2, 3], [1, 2, 4], fn($a, $b) => $a <=> $b);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayudiffassoc"></a>
### UnwrapArrayUdiffAssoc (*)
Set: Array

Unwraps `array_udiff_assoc` calls.

```php
$a = array_udiff_assoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayudiffuassoc"></a>
### UnwrapArrayUdiffUassoc (*)
Set: Array

Unwraps `array_udiff_uassoc` calls.

```php
$a = array_udiff_uassoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b, fn ($a, $b) => $a <=> $b);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayuintersect"></a>
### UnwrapArrayUintersect (*)
Set: Array

Unwraps `array_uintersect` calls.

```php
$a = array_uintersect([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayuintersectassoc"></a>
### UnwrapArrayUintersectAssoc (*)
Set: Array

Unwraps `array_uintersect_assoc` calls.

```php
$a = array_uintersect_assoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayuintersectuassoc"></a>
### UnwrapArrayUintersectUassoc (*)
Set: Array

Unwraps `array_uintersect_uassoc` calls.

```php
$a = array_uintersect_uassoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b, fn ($a, $b) => $a <=> $b);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayunique"></a>
### UnwrapArrayUnique (*)
Set: Array

Unwraps `array_unique` calls.

```php
$a = array_unique([1, 2, 3]);  // [tl! remove]
$a = [1, 2, 3];  // [tl! add]
```

<a name="unwraparrayvalues"></a>
### UnwrapArrayValues (*)
Set: Array

Unwraps `array_values` calls.

```php
$a = array_values(['a' => 1, 'b' => 2, 'c' => 3]);  // [tl! remove]
$a = ['a' => 1, 'b' => 2, 'c' => 3];  // [tl! add]
```

<a name="unwrapchop"></a>
### UnwrapChop (*)
Set: String

Unwraps `chop` calls.

```php
$a = chop('Hello World ', ' ');  // [tl! remove]
$a = 'Hello World ';  // [tl! add]
```

<a name="unwrapchunksplit"></a>
### UnwrapChunkSplit (*)
Set: String

Unwraps `chunk_split` calls.

```php
$a = chunk_split('Hello World', 1, ' ');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwraphtmlentitydecode"></a>
### UnwrapHtmlEntityDecode (*)
Set: String

Unwraps `html_entity_decode` calls.

```php
$a = html_entity_decode('&lt;h1&gt;Hello World&lt;/h1&gt;');  // [tl! remove]
$a = '&lt;h1&gt;Hello World&lt;/h1&gt;';  // [tl! add]
```

<a name="unwraphtmlentities"></a>
### UnwrapHtmlentities (*)
Set: String

Unwraps `htmlentities` calls.

```php
$a = htmlentities('<h1>Hello World</h1>');  // [tl! remove]
$a = '<h1>Hello World</h1>';  // [tl! add]
```

<a name="unwraphtmlspecialchars"></a>
### UnwrapHtmlspecialchars (*)
Set: String

Unwraps `htmlspecialchars` calls.

```php
$a = htmlspecialchars('<h1>Hello World</h1>');  // [tl! remove]
$a = '<h1>Hello World</h1>';  // [tl! add]
```

<a name="unwraphtmlspecialcharsdecode"></a>
### UnwrapHtmlspecialcharsDecode (*)
Set: String

Unwraps `htmlspecialchars_decode` calls.

```php
$a = htmlspecialchars_decode('&lt;h1&gt;Hello World&lt;/h1&gt;');  // [tl! remove]
$a = '&lt;h1&gt;Hello World&lt;/h1&gt;';  // [tl! add]
```

<a name="unwraplcfirst"></a>
### UnwrapLcfirst (*)
Set: String

Unwraps `lcfirst` calls.

```php
$a = lcfirst('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapltrim"></a>
### UnwrapLtrim (*)
Set: String

Unwraps `ltrim` calls.

```php
$a = ltrim(' Hello World');  // [tl! remove]
$a = ' Hello World';  // [tl! add]
```

<a name="unwrapmd5"></a>
### UnwrapMd5 (*)
Set: String

Unwraps `md5` calls.

```php
$a = md5('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapnl2br"></a>
### UnwrapNl2br (*)
Set: String

Unwraps `nl2br` calls.

```php
$a = nl2br('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwraprtrim"></a>
### UnwrapRtrim (*)
Set: String

Unwraps `rtrim` calls.

```php
$a = rtrim('Hello World ');  // [tl! remove]
$a = 'Hello World ';  // [tl! add]
```

<a name="unwrapstrireplace"></a>
### UnwrapStrIreplace (*)
Set: String

Unwraps `str_ireplace` calls.

```php
$a = str_ireplace('Hello', 'Hi', 'Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstrpad"></a>
### UnwrapStrPad (*)
Set: String

Unwraps `str_pad` calls.

```php
$a = str_pad('Hello World', 20, '-');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstrrepeat"></a>
### UnwrapStrRepeat (*)
Set: String

Unwraps `str_repeat` calls.

```php
$a = str_repeat('Hello World', 2);  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstrreplace"></a>
### UnwrapStrReplace (*)
Set: String

Unwraps `str_replace` calls.

```php
$a = str_replace('Hello', 'Hi', 'Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstrshuffle"></a>
### UnwrapStrShuffle (*)
Set: String

Unwraps `str_shuffle` calls.

```php
$a = str_shuffle('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstriptags"></a>
### UnwrapStripTags (*)
Set: String

Unwraps `strip_tags` calls.

```php
$a = strip_tags('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstrrev"></a>
### UnwrapStrrev (*)
Set: String

Unwraps `strrev` calls.

```php
$a = strrev('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstrtolower"></a>
### UnwrapStrtolower (*)
Set: String

Unwraps `strtolower` calls.

```php
$a = strtolower('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapstrtoupper"></a>
### UnwrapStrtoupper (*)
Set: String

Unwraps `strtoupper` calls.

```php
$a = strtoupper('Hello World');  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwrapsubstr"></a>
### UnwrapSubstr (*)
Set: String

Unwraps `substr` calls.

```php
$a = substr('Hello World', 0, 5);  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="unwraptrim"></a>
### UnwrapTrim (*)
Set: String

Unwraps `trim` calls.

```php
$a = trim(' Hello World ');  // [tl! remove]
$a = ' Hello World ';  // [tl! add]
```

<a name="unwrapucfirst"></a>
### UnwrapUcfirst (*)
Set: String

Unwraps `ucfirst` calls.

```php
$a = ucfirst('hello world');  // [tl! remove]
$a = 'hello world';  // [tl! add]
```

<a name="unwrapucwords"></a>
### UnwrapUcwords (*)
Set: String

Unwraps `ucwords` calls.

```php
$a = ucwords('hello world');  // [tl! remove]
$a = 'hello world';  // [tl! add]
```

<a name="unwrapwordwrap"></a>
### UnwrapWordwrap (*)
Set: String

Unwraps `wordwrap` calls.

```php
$a = wordwrap('Hello World', 5);  // [tl! remove]
$a = 'Hello World';  // [tl! add]
```

<a name="whilealwaysfalse"></a>
### WhileAlwaysFalse (*)
Set: ControlStructures

Makes the condition in a while loop always false.

```php
while ($a < 100) {  // [tl! remove]
while (false) {  // [tl! add]
    // ...
}
```


