<?php

declare(strict_types=1);

use Pest\Mutate\Support\StreamWrapper;

beforeEach(function (): void {
    $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pest-mutate-stream-wrapper-'.random_int(0, mt_getrandmax());
    mkdir($dir);

    $this->dir = $dir;
    $this->original = $dir.DIRECTORY_SEPARATOR.'Original.php';
    $this->override = $dir.DIRECTORY_SEPARATOR.'Override.php';
    $this->file = $dir.DIRECTORY_SEPARATOR.'data.txt';

    file_put_contents($this->original, '<?php // original');
    file_put_contents($this->override, '<?php // override');
    file_put_contents($this->file, '');

    StreamWrapper::start($this->original, $this->override);
});

afterEach(function (): void {
    StreamWrapper::disable();

    foreach ([$this->original, $this->override, $this->file] as $path) {
        @unlink($path);
    }
    @rmdir($this->dir);
});

it('takes the exclusive lock that was requested', function (): void {
    $holder = fopen($this->file, 'r+');
    $other = fopen($this->file, 'r+');

    expect(flock($holder, LOCK_EX))->toBeTrue()
        ->and(flock($other, LOCK_EX | LOCK_NB))->toBeFalse();

    flock($holder, LOCK_UN);
    fclose($holder);
    fclose($other);
});

it('releases the lock when asked to unlock', function (): void {
    $holder = fopen($this->file, 'r+');
    $other = fopen($this->file, 'r+');

    flock($holder, LOCK_EX);

    expect(flock($holder, LOCK_UN))->toBeTrue()
        ->and(flock($other, LOCK_EX | LOCK_NB))->toBeTrue();

    flock($other, LOCK_UN);
    fclose($holder);
    fclose($other);
});

it('writes with an exclusive lock while the wrapper is active', function (): void {
    expect(file_put_contents($this->file, 'mutated', LOCK_EX))->toBe(7)
        ->and(file_get_contents($this->file))->toBe('mutated');
});
