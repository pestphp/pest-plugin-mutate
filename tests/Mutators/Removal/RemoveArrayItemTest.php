<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Removal\RemoveArrayItem;

it('mutates an array by removing an item', function (): void {
    expect(mutateCode(RemoveArrayItem::class, <<<'CODE'
        <?php

        return ['foo' => 'bar'];
        CODE))->toBe(<<<'CODE'
        <?php
        
        return [];
        CODE);
});
