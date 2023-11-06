<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Repositories\MutationRepository;

class MutationSuite
{
    private static ?MutationSuite $instance = null;

    public readonly MutationRepository $repository;

    private float $start;

    private float $finish;

    public function __construct()
    {
        $this->repository = new MutationRepository();
    }

    public static function instance(): self
    {
        if (! self::$instance instanceof \Pest\Mutate\MutationSuite) {
            self::$instance = new MutationSuite();
        }

        return self::$instance;
    }

    public function duration(): float
    {
        return $this->finish - $this->start;
    }

    public function trackStart(): void
    {
        $this->start = microtime(true);
    }

    public function trackFinish(): void
    {
        $this->finish = microtime(true);
    }
}
