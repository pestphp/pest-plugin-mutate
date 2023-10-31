<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\ControlStructures\BreakToContinue;

it('mutates break to continue', function (): void {
    expect(mutateCode(BreakToContinue::class, <<<'CODE'
        <?php

        foreach ($items as $item) {
            if ($item === 'foo') {
                break;
            }
            echo $item;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        foreach ($items as $item) {
            if ($item === 'foo') {
                continue;
            }
            echo $item;
        }
        CODE);
});
