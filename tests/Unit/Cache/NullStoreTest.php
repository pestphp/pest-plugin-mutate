<?php

declare(strict_types=1);

use Pest\Mutate\Cache\NullStore;

beforeEach(function (): void {
    $this->cache = new NullStore();
    $this->key = 'my-key';
});

it('always returns returns the default value when trying to load something from cache', function (): void {
    expect($this->cache->get($this->key, 'bar'))
        ->toBe('bar');

    expect($this->cache->get($this->key))
        ->toBeNull();

    $this->cache->set($this->key, 'foo');

    expect($this->cache->get($this->key, 'bar'))
        ->toBe('bar');
});

it('always return true when adding a value', function (): void {
    expect($this->cache->set($this->key, 'foo'))
        ->toBeTrue();
});

it('always return true when deleteing a value', function (): void {
    expect($this->cache->delete($this->key))
        ->toBeTrue();
});

it('always return true when clearing the cache', function (): void {
    expect($this->cache->clear())
        ->toBeTrue();
});

it('always returns returns the default value when trying to load multiple values from cache', function (): void {
    expect($this->cache->getMultiple(['a', 'b'], 'bar'))
        ->toBe(['a' => 'bar', 'b' => 'bar']);

    expect($this->cache->getMultiple(['a', 'b']))
        ->toBe(['a' => null, 'b' => null]);

    $this->cache->set('a', 'foo');

    expect($this->cache->getMultiple(['a', 'b'], 'bar'))
        ->toBe(['a' => 'bar', 'b' => 'bar']);
});

it('always return true when adding multiple values', function (): void {
    expect($this->cache->setMultiple(['a' => 'foo', 'b' => 'bar']))
        ->toBeTrue();
});

it('always return true when deleteing multiple values', function (): void {
    expect($this->cache->deleteMultiple(['a', 'b']))
        ->toBeTrue();
});

it('always return false when checking if a value is in the cache', function (): void {
    expect($this->cache->has($this->key))
        ->toBeFalse();
});
