<?php

declare(strict_types=1);

use Pest\Mutate\Support\StreamWrapper;

it('takes the exclusive lock a write asks for, so the write succeeds', function (): void {
    $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pest-mutate-stream-lock-'.getmypid().'.txt';

    $failure = null;
    set_error_handler(function (int $level, string $message) use (&$failure): bool {
        $failure = $message;

        return true;
    });

    StreamWrapper::start(__FILE__, __FILE__);

    try {
        $written = file_put_contents($path, 'hello', LOCK_EX);
    } finally {
        StreamWrapper::disable();
        restore_error_handler();
    }

    $contents = is_file($path) ? file_get_contents($path) : false;
    @unlink($path);

    expect($failure)->toBeNull()
        ->and($written)->toBe(5)
        ->and($contents)->toBe('hello');
});
