<?php

declare(strict_types=1);

$phpstan = dirname(__DIR__) . '/vendor/bin/phpstan';
$config = __DIR__ . '/phpstan-unchecked-merge.neon';

$command = sprintf(
    '%s %s analyse -c %s --no-progress --error-format=raw 2>&1',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($phpstan),
    escapeshellarg($config),
);

$output = [];
$exitCode = 0;
exec($command, $output, $exitCode);

$joined = implode("\n", $output);

if ($exitCode === 0) {
    fwrite(STDERR, "Expected PHPStan to fail on DomainCheckedException, but analysis passed.\n");
    exit(1);
}

$hasChecked = str_contains($joined, 'DomainCheckedException')
    || str_contains($joined, 'missingType.checkedException');

if (! $hasChecked) {
    fwrite(
        STDERR,
        "Expected missing @throws for DomainCheckedException (missingType.checkedException), got:\n{$joined}\n",
    );
    exit(1);
}

$mergeBroken = [];

if (str_contains($joined, 'throwsRuntime') || preg_match('/RuntimeException.*@throws|@throws.*RuntimeException/i', $joined) === 1) {
    // Only flag if the RuntimeException missing-throws is about our probe method
    if (str_contains($joined, 'throwsRuntime') || str_contains($joined, 'UncheckedMergeProbe::throwsRuntime')) {
        $mergeBroken[] = 'RuntimeException still reported as checked (throwsRuntime) — shared uncheckedExceptionClasses did not merge';
    }
}

if (str_contains($joined, 'throwsLogic') || str_contains($joined, 'UncheckedMergeProbe::throwsLogic')) {
    $mergeBroken[] = 'LogicException still reported as checked (throwsLogic) — shared uncheckedExceptionClasses did not merge';
}

if (str_contains($joined, 'throwsConsumerUnchecked') || str_contains($joined, 'UncheckedMergeProbe::throwsConsumerUnchecked')) {
    $mergeBroken[] = 'ConsumerUncheckedException reported as checked — consumer uncheckedExceptionClasses not applied';
}

// Stronger line-level check: any error line mentioning those methods
foreach ($output as $line) {
    if (str_contains($line, 'throwsRuntime') && str_contains($line, 'RuntimeException')) {
        $mergeBroken[] = 'RuntimeException reported on throwsRuntime — merge replaced shared list';
    }

    if (str_contains($line, 'throwsLogic') && str_contains($line, 'LogicException')) {
        $mergeBroken[] = 'LogicException reported on throwsLogic — merge replaced shared list';
    }

    if (str_contains($line, 'throwsConsumerUnchecked') && str_contains($line, 'ConsumerUncheckedException')) {
        $mergeBroken[] = 'ConsumerUncheckedException reported on throwsConsumerUnchecked';
    }
}

$mergeBroken = array_values(array_unique($mergeBroken));

if ($mergeBroken !== []) {
    fwrite(STDERR, implode("\n", $mergeBroken) . "\n\nFull output:\n{$joined}\n");
    exit(1);
}

echo "uncheckedExceptionClasses merge is active.\n";
