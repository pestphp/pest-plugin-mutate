<?php

declare(strict_types=1);

uses()
    ->beforeEach(function () {
        \Pest\Support\Container::getInstance()->get(\Pest\Mutate\Profiles::class)->reset();
    })->in(__DIR__);
