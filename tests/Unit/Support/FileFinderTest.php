<?php

declare(strict_types=1);

use Pest\Mutate\Support\FileFinder;

it('finds all files in a directory', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], []))
        ->toHaveCount(2)
        ->getIterator()->current()->getRealPath()->toEndWith('AgeHelper.php');

    expect(FileFinder::files([getcwd().'/tests/Fixtures'], []))
        ->toHaveCount(2)
        ->getIterator()->current()->getRealPath()->toEndWith('AgeHelper.php');
});

it('finds files by path', function (): void {
    expect(FileFinder::files(['tests/Fixtures/Classes/SizeHelper.php'], []))
        ->toHaveCount(1)
        ->getIterator()->current()->getRealPath()->toEndWith('SizeHelper.php');
});

it('excludes a file by full path', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['tests/Fixtures/Classes/AgeHelper.php']))
        ->toHaveCount(1)
        ->getIterator()->current()->getRealPath()->toEndWith('SizeHelper.php');
});

it('excludes a file by relative path', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['Classes/AgeHelper.php']))
        ->toHaveCount(1)
        ->getIterator()->current()->getRealPath()->toEndWith('SizeHelper.php');
});

it('excludes a file by pattern', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['Classes/Age*.php']))
        ->toHaveCount(1)
        ->getIterator()->current()->getRealPath()->toEndWith('SizeHelper.php');
});

it('excludes a file by pattern ending with an asterisk', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['Classes/Age*']))
        ->toHaveCount(1)
        ->getIterator()->current()->getRealPath()->toEndWith('SizeHelper.php');
});

it('does not exclude files with an incomplete path', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['Classes/Age']))
        ->toHaveCount(2);
});

it('excludes a directory by full path', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['tests/Fixtures/Classes']))
        ->toBeEmpty();
});

it('excludes a directory by relative path', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['Classes']))
        ->toBeEmpty();

    expect(FileFinder::files(['tests/Fixtures'], ['Classes/']))
        ->toBeEmpty();
});

it('excludes a directory by pattern', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['tests/*/Classes']))
        ->toBeEmpty();
});

it('excludes a directory by pattern with multiple wildcards', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['*/*/Classes']))
        ->toBeEmpty();
});

it('excludes a directory by pattern with double asterisk wildcard', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['**/Classes']))
        ->toBeEmpty();
});

it('excludes by absolute path', function (): void {
    expect(FileFinder::files(['tests/Fixtures'], ['/tests/Fixtures/Classes']))
        ->toBeEmpty();

    expect(FileFinder::files(['tests/Fixtures'], [getcwd().'/tests/Fixtures/Classes']))
        ->toBeEmpty();

    expect(FileFinder::files(['tests/Fixtures'], ['/tests/Fixtures/Invalid']))
        ->not->toBeEmpty();

    expect(FileFinder::files(['tests/Fixtures'], ['/tests/Fixtures/Classes/AgeHelper.php']))
        ->toHaveCount(1);
});
