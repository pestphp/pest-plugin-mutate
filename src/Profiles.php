<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Support\Container;

class Profiles
{
    /**
     * @var array<string, Profile>
     */
    private array $profiles = [];

    public static function get(string $name): Profile
    {
        return Container::getInstance()->get(Profiles::class) // @phpstan-ignore-line
            ->getProfile($name);
    }

    public function getProfile(string $name): Profile
    {
        if (! array_key_exists($name, $this->profiles)) {
            $this->profiles[$name] = new Profile();
        }

        return $this->profiles[$name];
    }

    /**
     * This is only for testing purposes.
     *
     * @internal
     */
    public function reset(): void
    {
        $this->profiles = [];
    }
}
