<?php

declare(strict_types=1);

use Pest\Mutate\Profiles;
use Pest\Support\Container;

uses()
    ->beforeEach(function (): void {
        Container::getInstance()->get(Profiles::class)->resetFakeProfile();
    })->in(__DIR__);
