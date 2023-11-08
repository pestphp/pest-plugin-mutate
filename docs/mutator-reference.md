# Mutator Reference

A comprehensive list of all mutators available.

## Sets

The mutators are grouped in sets.

### Default

A set of mutators that are enabled by default. Which keeps a good balance between performance and mutation coverage.

This set consists of various mutators from different sets. Mutators included are indicated with a asterisk (*).

___SETS___

## Mutators

An alphabetical list of all mutators.

___MUTATORS___

<a name="minus-to-plus"></a>
### Arithmetic: MinusToPlus

Converts all arithmetic minus operations to plus operations.

```php
$c = $a - $b;  // [tl! remove]
$c = $a + $b;  // [tl! add]
```

<a name="plus-to-minus"></a>
### PlusToMinus (*)
Set: Arithmetic

Converts all arithmetic plus operations to minus operations.

```php
$c = $a + $b;  // [tl! remove]
$c = $a - $b;  // [tl! add]
```

<a name="if-always-false"></a>
### IfAlwaysFalse

Sets all `if()` conditions to false.

```php
if ($a > $b) {  // [tl! remove]
if (false) {  // [tl! add]
    return true;
}
```

<a name="if-always-true"></a>
### IfAlwaysTrue

Sets all `if()` conditions to true.

```php
if ($a > $b) {  // [tl! remove]
if (true) {  // [tl! add]
    return true;
}
```

---

In this chapter, we've seen XYZ. In the following chapter, we will dive into XYZ: [XYZ](/docs/xyz)
