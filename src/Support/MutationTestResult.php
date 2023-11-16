<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

enum MutationTestResult
{
    case None;
    case NotCovered;
    case Killed;
    case Survived;
    case Timeout;
}
