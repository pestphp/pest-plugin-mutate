<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Repositories\MutationRepository;

class MutationSuite
{
    private static ?MutationSuite $instance = null;

    public readonly MutationRepository $repository;

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
}
