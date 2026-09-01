<?php

declare(strict_types=1);

$phpstan = dirname(__DIR__) . '/vendor/bin/phpstan';
$config = __DIR__ . '/phpstan-expect-new-static.neon';

$command = sprintf(
    '%s %s analyse -c %s --no-progress 2>&1',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($phpstan),
    escapeshellarg($config),
);

$output = [];
$exitCode = 0;
exec($command, $output, $exitCode);

$joined = implode("\n", $output);

if ($exitCode === 0) {
    fwrite(STDERR, "Expected PHPStan to fail with new.static, but analysis passed.\n");
    exit(1);
}

if (! str_contains($joined, 'new.static')) {
    fwrite(STDERR, "Expected new.static in PHPStan output, got:\n{$joined}\n");
    exit(1);
}

echo "new.static rule is active.\n";
