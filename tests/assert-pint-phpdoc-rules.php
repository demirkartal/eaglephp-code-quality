<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$pint = $root . '/vendor/bin/pint';
$config = $root . '/pint.json';
$probe = $root . '/tests/fixtures/PintPhpdocProbe.php';

$violations = <<<'PHP'
<?php

declare(strict_types=1);

namespace EaglePhpCodeQuality\Tests\Fixtures;

/**
 * Bad probe.
 *
 * @package EaglePhpCodeQuality
 * @access public
 */
final class PintPhpdocProbeSmoke
{
    /** @var string[] */
    private array $items = [];

    /**
     * @param int $count Item count
     * @param string $name Display name
     * @return void
     */
    public function build(string $name, int $count): array
    {
        return [$name, (string) $count];
    }
}

PHP;

$temp = tempnam(sys_get_temp_dir(), 'pint-phpdoc-probe-');

if ($temp === false) {
    fwrite(STDERR, "Failed to create temp file.\n");
    exit(1);
}

$tempFile = $temp . '.php';
rename($temp, $tempFile);
file_put_contents($tempFile, $violations);

$command = sprintf(
    '%s %s --config %s %s 2>&1',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($pint),
    escapeshellarg($config),
    escapeshellarg($tempFile),
);

$output = [];
$exitCode = 0;
exec($command, $output, $exitCode);

$fixed = (string) file_get_contents($tempFile);
@unlink($tempFile);

if ($exitCode !== 0) {
    fwrite(STDERR, "Pint failed to fix probe:\n" . implode("\n", $output) . "\n");
    exit(1);
}

$checks = [
    'modern array notation' => (bool) preg_match('/@(var|param|return)\s[^\n]*(array<|list<)/', $fixed),
    'no @access' => ! str_contains($fixed, '@access'),
    'no @package' => ! str_contains($fixed, '@package'),
    'no @return void' => ! str_contains($fixed, '@return void'),
    'param order' => (bool) preg_match('/@param\s+string\s+\$name[\s\S]*@param\s+int\s+\$count/s', $fixed),
];

$failed = [];

foreach ($checks as $label => $passed) {
    if (! $passed) {
        $failed[] = $label;
    }
}

if ($failed !== []) {
    fwrite(STDERR, "Pint PHPDoc smoke checks failed: " . implode(', ', $failed) . "\n");
    fwrite(STDERR, $fixed . "\n");
    exit(1);
}

$goldenCommand = sprintf(
    '%s %s --config %s --test %s 2>&1',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($pint),
    escapeshellarg($config),
    escapeshellarg($probe),
);

exec($goldenCommand, $goldenOutput, $goldenExit);

if ($goldenExit !== 0) {
    fwrite(STDERR, "Golden PintPhpdocProbe.php is not compliant:\n" . implode("\n", $goldenOutput) . "\n");
    exit(1);
}

echo "Pint PHPDoc rules smoke test passed.\n";
