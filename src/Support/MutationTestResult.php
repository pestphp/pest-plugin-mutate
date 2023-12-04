<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

enum MutationTestResult: string
{
    case None = 'none';
    case NotCovered = 'not-covered';
    case Killed = 'killed';
    case Survived = 'survived';
    case Timeout = 'timeout';
}
