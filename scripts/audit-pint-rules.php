<?php

declare(strict_types=1);

/**
 * Audit pint.json explicit rules against recommended PHPDoc/PER gaps.
 *
 * The `per` preset already includes PER-CS (superset of PSR-12); this script
 * checks explicit rule overrides and documents preset coverage.
 */
$root = dirname(__DIR__);
$configPath = $root . '/pint.json';

if (! is_file($configPath)) {
    fwrite(STDERR, "Missing pint.json at {$configPath}\n");
    exit(1);
}

/** @var array{preset?: string, risky?: bool, rules?: array<string, mixed>} $config */
$config = json_decode((string) file_get_contents($configPath), true, 512, JSON_THROW_ON_ERROR);

$preset = $config['preset'] ?? 'laravel';
$explicitRules = array_keys($config['rules'] ?? []);

$recommendedPhpdoc = [
    'align_multiline_comment',
    'phpdoc_array_type',
    'phpdoc_inline_tag_normalizer',
    'phpdoc_list_type',
    'phpdoc_no_access',
    'phpdoc_no_empty_return',
    'phpdoc_no_package',
    'phpdoc_param_order',
    'phpdoc_single_line_var_spacing',
    'phpdoc_summary',
    'phpdoc_tag_casing',
    'phpdoc_trim_consecutive_blank_line_separation',
];

$baselinePhpdoc = [
    'no_empty_phpdoc',
    'no_superfluous_phpdoc_tags',
    'phpdoc_align',
    'phpdoc_indent',
    'phpdoc_order',
    'phpdoc_scalar',
    'phpdoc_separation',
    'phpdoc_trim',
    'phpdoc_types',
    'phpdoc_types_order',
];

$missingRecommended = array_values(array_diff($recommendedPhpdoc, $explicitRules));
$presentRecommended = array_values(array_intersect($recommendedPhpdoc, $explicitRules));
$missingBaseline = array_values(array_diff($baselinePhpdoc, $explicitRules));

echo "# Pint rules audit\n\n";
echo "- Preset: `{$preset}` (PER-CS includes PSR-12 / PSR-1 foundation)\n";
echo '- Risky: ' . (($config['risky'] ?? false) ? 'true' : 'false') . "\n";
echo '- Explicit rules: ' . count($explicitRules) . "\n\n";

echo "## Recommended PHPDoc rules (explicit in pint.json)\n\n";

if ($presentRecommended === []) {
    echo "_None enabled yet._\n\n";
} else {
    foreach ($presentRecommended as $rule) {
        echo "- [x] `{$rule}`\n";
    }

    echo "\n";
}

if ($missingRecommended !== []) {
    echo "## Missing recommended PHPDoc rules\n\n";

    foreach ($missingRecommended as $rule) {
        echo "- [ ] `{$rule}`\n";
    }

    echo "\n";
} else {
    echo "All recommended PHPDoc rules are present in pint.json.\n\n";
}

if ($missingBaseline !== []) {
    echo "## Missing baseline PHPDoc rules\n\n";

    foreach ($missingBaseline as $rule) {
        echo "- [ ] `{$rule}`\n";
    }

    echo "\n";
}

echo "## Preset coverage (informational)\n\n";
echo "Rules such as `concat_space`, `array_syntax`, `visibility_required`, and `method_argument_space` come from the `{$preset}` preset — no duplicate explicit entry required.\n";

exit($missingRecommended === [] && $missingBaseline === [] ? 0 : 1);
