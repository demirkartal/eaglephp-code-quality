# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.5] - 2026-09-02

### Added

- Pint PHPDoc rules: `phpdoc_array_type`, `phpdoc_list_type`, `phpdoc_summary`, `phpdoc_param_order`, deprecated tag removal (`phpdoc_no_access`, `phpdoc_no_package`), and related hygiene fixers — aligned with PHPStan Level 10 modern type notation.
- `scripts/audit-pint-rules.php` and `composer audit:pint` — reports explicit rule coverage vs recommended PHPDoc set.
- Pint PHPDoc smoke test (`composer format:rules-smoke`) and golden fixture.

## [1.0.4] - 2026-09-01

### Added

- Enable `reportUnsafeArrayStringKeyCasting: detect` in the shared ruleset — narrows array key types when decimal-int string keys may be silently cast to `int` at runtime (PHPStan 2.2+).

## [1.0.3] - 2026-09-01

### Changed

- Remove redundant `checkDynamicProperties` and `reportWrongPhpDocTypeInVarTag` from `phpstan.neon` — already enabled by `phpstan-strict-rules` (no behaviour change).
- README: document PHPStan “Want to go further?” alignment, strict-rules overlap, optional consumer overrides, and expanded core stack version table.

## [1.0.2] - 2026-09-01

### Fixed

- Include `phar://phpstan.phar/conf/config.level10.neon` so built-in PHPStan level rules (including `new.static` / `NewStaticRule`) are registered when `customRulesetUsed: true`. Previously only `parameters.level: 10` was set, leaving `usedLevel` at 0 and silently skipping level-0 rules.

### Added

- Regression fixture asserting `new.static` is reported for unsafe `new static()` usage.

## [1.0.1] - 2026-09-01

### Fixed

- Set `customRulesetUsed: true` in package `phpstan.neon` so PHPStan 2.x does not report "No rules detected" when the base ruleset is loaded via extension-installer.
- `phpstan-next.neon` no longer re-includes `phpstan.neon`; consumer `phpstan.next.neon` can safely include both project config and the vendor next profile without duplicate includes.

## [1.0.0] - 2026-09-01

### Added

- PHPStan Level 10 shared ruleset (`phpstan.neon`) with bleedingEdge, strict-rules, phpunit, deprecation-rules, and type-coverage.
- Type Perfect narrowing rules (`no_empty_on_object`, `no_isset_on_object`, `no_param_type_removal`).
- Forward-compat profile (`phpstan-next.neon`) targeting PHP 8.5 (`80500`).
- Laravel Pint config (`pint.json`) with `per` preset and strict import/PHPDoc rules.
- Composer `phpstan-extension` registration via `phpstan/extension-installer`.
- CI workflow: Composer validate, PHPStan integration fixture, Pint config check (PHP 8.4 and 8.5).

[1.0.5]: https://github.com/demirkartal/eaglephp-code-quality/releases/tag/v1.0.5
[1.0.4]: https://github.com/demirkartal/eaglephp-code-quality/releases/tag/v1.0.4
[1.0.3]: https://github.com/demirkartal/eaglephp-code-quality/releases/tag/v1.0.3
[1.0.2]: https://github.com/demirkartal/eaglephp-code-quality/releases/tag/v1.0.2
[1.0.1]: https://github.com/demirkartal/eaglephp-code-quality/releases/tag/v1.0.1
[1.0.0]: https://github.com/demirkartal/eaglephp-code-quality/releases/tag/v1.0.0
