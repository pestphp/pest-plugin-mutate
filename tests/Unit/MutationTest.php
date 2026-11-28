<?php

declare(strict_types=1);

use Pest\Mutate\Mutation;

beforeEach(function (): void {
    $this->directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pest-mutate-'.bin2hex(random_bytes(6));
});

afterEach(function (): void {
    $remove = function (string $path) use (&$remove): void {
        if (is_dir($path)) {
            foreach (scandir($path) ?: [] as $entry) {
                if ($entry !== '.' && $entry !== '..') {
                    $remove($path.DIRECTORY_SEPARATOR.$entry);
                }
            }

            rmdir($path);
        } elseif (file_exists($path)) {
            unlink($path);
        }
    };

    $remove($this->directory);
});

it('writes the modified source and returns its path', function (): void {
    mkdir($this->directory);

    $path = Mutation::writeModifiedSource($this->directory, '<?php echo 1;');

    expect($path)->toStartWith($this->directory)
        ->and(file_get_contents($path))->toBe('<?php echo 1;');
});

it('creates the directory when it is missing', function (): void {
    $directory = $this->directory.DIRECTORY_SEPARATOR.'nested'.DIRECTORY_SEPARATOR.'mutations';

    $path = Mutation::writeModifiedSource($directory, '<?php echo 2;');

    expect($directory)->toBeDirectory()
        ->and(file_get_contents($path))->toBe('<?php echo 2;');
});

it('throws when the directory cannot be created', function (): void {
    file_put_contents($this->directory, 'a regular file where the directory should be');

    Mutation::writeModifiedSource($this->directory, '<?php echo 3;');
})->throws(RuntimeException::class, 'Unable to create the mutations directory');

it('throws when the modified source cannot be written', function (): void {
    mkdir($this->directory);
    mkdir($this->directory.DIRECTORY_SEPARATOR.hash('xxh3', '<?php echo 4;'));

    Mutation::writeModifiedSource($this->directory, '<?php echo 4;');
})->throws(RuntimeException::class, 'Unable to write the modified source');
