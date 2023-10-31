<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\ControlStructures\ContinueToBreak;

it('mutates continue to break', function (): void {
    expect(mutateCode(ContinueToBreak::class, <<<'CODE'
        <?php

        foreach ($items as $item) {
            if ($item === 'foo') {
                continue;
            }
            echo $item;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        foreach ($items as $item) {
            if ($item === 'foo') {
                break;
            }
            echo $item;
        }
        CODE);
});
