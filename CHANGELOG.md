# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-09-01

### Added

- PHPStan Level 10 shared ruleset (`phpstan.neon`) with bleedingEdge, strict-rules, phpunit, deprecation-rules, and type-coverage.
- Type Perfect narrowing rules (`no_empty_on_object`, `no_isset_on_object`, `no_param_type_removal`).
- Forward-compat profile (`phpstan-next.neon`) targeting PHP 8.5 (`80500`).
- Laravel Pint config (`pint.json`) with `per` preset and strict import/PHPDoc rules.
- Composer `phpstan-extension` registration via `phpstan/extension-installer`.
- CI workflow: Composer validate, PHPStan integration fixture, Pint config check (PHP 8.4 and 8.5).

[1.0.0]: https://github.com/demirkartal/eaglephp-code-quality/releases/tag/v1.0.0
