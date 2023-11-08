<?php

declare(strict_types=1);

use Pest\Mutate\Repositories\ConfigurationRepository;

it('uses the paths from the phpunit.xml if no paths are configured', function (): void {
    $repository = new ConfigurationRepository();

    expect($repository->mergedConfiguration())
        ->paths->toBeArray()
        ->paths->{0}->toEndWith('/./src');
});
