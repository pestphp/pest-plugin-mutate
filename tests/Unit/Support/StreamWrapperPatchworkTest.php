<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

beforeEach(function (): void {
    $this->dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pest-mutate-patchwork-'.bin2hex(random_bytes(6));
    mkdir($this->dir);
});

afterEach(function (): void {
    foreach (glob($this->dir.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
        unlink($file);
    }

    rmdir($this->dir);
});

it('keeps Patchwork in front while a file opened through the mutation wrapper stays open', function (): void {
    $vendor = dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'vendor';

    $files = [
        'Original.php' => "<?php\n",
        'Override.php' => "<?php\n",
        'data.txt' => 'data',
        'functions.php' => "<?php\n\nfunction pest_mutate_patchwork_greet(): string\n{\n    return 'original';\n}\n",
        'child.php' => sprintf(
            <<<'PHP'
                <?php

                require %s;

                Pest\Mutate\Support\StreamWrapper::start(__DIR__.'/Original.php', __DIR__.'/Override.php');

                require %s;

                $report = fopen(__DIR__.'/data.txt', 'r');

                require __DIR__.'/functions.php';

                Patchwork\redefine('pest_mutate_patchwork_greet', fn (): string => 'redefined');

                echo pest_mutate_patchwork_greet();

                fclose($report);

                PHP,
            var_export($vendor.DIRECTORY_SEPARATOR.'autoload.php', true),
            var_export($vendor.DIRECTORY_SEPARATOR.'antecedent'.DIRECTORY_SEPARATOR.'patchwork'.DIRECTORY_SEPARATOR.'Patchwork.php', true),
        ),
    ];

    foreach ($files as $name => $contents) {
        file_put_contents($this->dir.DIRECTORY_SEPARATOR.$name, $contents);
    }

    $process = new Process([PHP_BINARY, $this->dir.DIRECTORY_SEPARATOR.'child.php'], $this->dir);
    $process->run();

    expect($process->getOutput().$process->getErrorOutput())->toBe('redefined');
});
