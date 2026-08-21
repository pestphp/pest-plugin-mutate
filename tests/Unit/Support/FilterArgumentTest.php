<?php

declare(strict_types=1);

use Pest\Mutate\Support\FilterArgument;

/**
 * Every assertion here is about one of two things: that the encoding selects the SAME
 * tests, or that when it cannot, it selects MORE of them and says so. The failure being
 * guarded is silent in both directions — a filter that selects too few would fabricate
 * survivors, and one that selects too many without saying so would make the score certify
 * more than the run measured.
 */

/** Match a test name the way PHPUnit applies `--filter`: the joined value as one regex. */
function selects(string $argument, string $testName): bool
{
    $pattern = (string) preg_replace('/^--filter="(.*)"$/s', '$1', $argument);

    return preg_match('/'.$pattern.'/', $testName) === 1;
}

it('factors a shared class prefix', function (): void {
    $filter = FilterArgument::for([
        'SizeHelperTest::(.*)it_is_small',
        'SizeHelperTest::(.*)it_is_large',
    ]);

    expect($filter->argument)->toBe('--filter="SizeHelperTest::(.*)(it_is_small|it_is_large)"')
        ->and($filter->widened)->toBe([]);
});

it('factors losslessly — the factored form selects exactly what the naive form selected', function (): void {
    $fragments = [
        'SizeHelperTest::(.*)it_is_small',
        'SizeHelperTest::(.*)it_is_large',
        'AgeHelperTest::(.*)it_is_adult',
    ];

    $names = [
        'Tests\Unit\SizeHelperTest::it_is_small',
        'Tests\Unit\SizeHelperTest::it_is_large',
        'Tests\Unit\AgeHelperTest::it_is_adult',
        'Tests\Unit\SizeHelperTest::it_is_medium',   // never selected by either form
        'Tests\Unit\OtherTest::it_is_small',         // wrong class, same tail
    ];

    $naive = '--filter="'.implode('|', $fragments).'"';
    $factored = FilterArgument::for($fragments)->argument;

    $before = array_values(array_filter($names, fn (string $n): bool => selects($naive, $n)));
    $after = array_values(array_filter($names, fn (string $n): bool => selects($factored, $n)));

    expect($after)->toBe($before)->toHaveCount(3);
});

it('keeps the inner parentheses, without which the alternation would bind to the whole pattern', function (): void {
    // ⚠️ THE ONE DETAIL THAT MAKES FACTORING SAFE. `Cls::(.*)a|b` is `(Cls::(.*)a)|(b)`,
    // so a bare `b` anywhere matches and the filter runs tests it was never given.
    $broken = '--filter="SizeHelperTest::(.*)it_is_small|it_is_large"';
    $correct = FilterArgument::for([
        'SizeHelperTest::(.*)it_is_small',
        'SizeHelperTest::(.*)it_is_large',
    ])->argument;

    expect(selects($broken, 'Tests\Unit\OtherTest::it_is_large'))->toBeTrue()
        ->and(selects($correct, 'Tests\Unit\OtherTest::it_is_large'))->toBeFalse();
});

it('leaves a fragment it does not recognise exactly as it found it', function (): void {
    $filter = FilterArgument::for([
        'SizeHelperTest::(.*)it_is_small',
        'something upstream started emitting',
    ]);

    expect($filter->argument)->toContain('something upstream started emitting');
});

it('de-duplicates repeated tails rather than repeating them', function (): void {
    $filter = FilterArgument::for([
        'SizeHelperTest::(.*)it_is_small',
        'SizeHelperTest::(.*)it_is_small',
    ]);

    expect($filter->argument)->toBe('--filter="SizeHelperTest::(.*)(it_is_small)"');
});

it('stays under the kernel cap on a filter that would otherwise exceed it', function (): void {
    // The measured shape of the defect: one class reached by a very large number of tests.
    // 2,076 covering tests produced a 172,779-byte argument against a 131,072-byte cap.
    $fragments = [];

    for ($i = 0; $i < 3000; $i++) {
        $fragments[] = 'ComponentRenderTest::(.*)it_renders_the_component_variant_number_'.$i;
    }

    $naive = '--filter="'.implode('|', $fragments).'"';

    expect(strlen($naive))->toBeGreaterThan(131072);

    $filter = FilterArgument::for($fragments);

    expect(strlen($filter->argument))->toBeLessThanOrEqual(FilterArgument::BUDGET_BYTES);
});

it('collapses the heaviest class first, so the fewest collapses buy the most room', function (): void {
    $fragments = ['SmallTest::(.*)it_does_one_thing'];

    for ($i = 0; $i < 4000; $i++) {
        $fragments[] = 'HugeTest::(.*)it_renders_a_very_long_test_name_number_'.$i;
    }

    $filter = FilterArgument::for($fragments);

    expect(array_keys($filter->widened))->toBe(['HugeTest'])
        ->and($filter->argument)->toContain('SmallTest::(.*)(it_does_one_thing)')
        ->and($filter->argument)->toContain('HugeTest::');
});

it('widens rather than narrows — every originally-selected test still matches', function (): void {
    $fragments = [];

    for ($i = 0; $i < 4000; $i++) {
        $fragments[] = 'HugeTest::(.*)it_renders_a_very_long_test_name_number_'.$i;
    }

    $filter = FilterArgument::for($fragments);

    expect($filter->widened)->not->toBe([]);

    foreach ([0, 1999, 3999] as $i) {
        expect(selects($filter->argument, 'Tests\Unit\HugeTest::it_renders_a_very_long_test_name_number_'.$i))
            ->toBeTrue();
    }
});

it('reports what it widened instead of widening silently', function (): void {
    $fragments = [];

    for ($i = 0; $i < 4000; $i++) {
        $fragments[] = 'HugeTest::(.*)it_renders_a_very_long_test_name_number_'.$i;
    }

    $filter = FilterArgument::for($fragments);

    expect($filter->widened)->toBe(['HugeTest' => 4000])
        ->and($filter->notice())->toContain('HugeTest (+4000 tests)')
        ->and($filter->notice())->toContain('superset');
});

it('says nothing when nothing was widened', function (): void {
    expect(FilterArgument::for(['SizeHelperTest::(.*)it_is_small'])->notice())->toBeNull();
});

it('fails loudly rather than returning a filter that still does not fit', function (): void {
    // Dropping the filter would run the whole suite per mutant and read as a fast green,
    // so the one thing this must never do is return something over budget.
    $fragments = [];

    for ($i = 0; $i < 200; $i++) {
        $fragments[] = 'Test'.str_repeat('X', 400).$i.'::(.*)it_does_something';
    }

    expect(fn (): FilterArgument => FilterArgument::for($fragments, 1000))
        ->toThrow(RuntimeException::class, 'over the 1000-byte budget');
});

it('handles an empty selection without inventing one', function (): void {
    expect(FilterArgument::for([])->argument)->toBe('--filter=""');
});
