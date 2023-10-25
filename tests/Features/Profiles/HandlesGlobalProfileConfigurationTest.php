<?php

declare(strict_types=1);

use Pest\Mutate\Profiles;

test('configure default profile globally', function () {
    mutate()
        ->min(10.0);

    $profile = Profiles::get('default');

    expect($profile->minMSI)
        ->toEqual(10.0);
});

test('configure non default profile globally', function () {
    mutate('profile-1')
        ->min(20.0);

    $profile = Profiles::get('profile-1');

    expect($profile->minMSI)
        ->toEqual(20.0);
});
