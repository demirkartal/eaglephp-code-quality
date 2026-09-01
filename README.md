# EaglePHP Code Quality

[![Latest Stable Version](https://poser.pugx.org/demirkartal/eaglephp-code-quality/v/stable)](https://packagist.org/packages/demirkartal/eaglephp-code-quality)
[![Total Downloads](https://poser.pugx.org/demirkartal/eaglephp-code-quality/downloads)](https://packagist.org/packages/demirkartal/eaglephp-code-quality)
[![License](https://poser.pugx.org/demirkartal/eaglephp-code-quality/license)](https://packagist.org/packages/demirkartal/eaglephp-code-quality)

Shared PHPStan Level 10 ruleset and Laravel Pint configuration for EaglePHP ecosystem projects.

## Installation

```bash
composer require --dev demirkartal/eaglephp-code-quality
```

Enable the extension installer plugin in your project `composer.json`:

```json
{
  "config": {
    "allow-plugins": {
      "phpstan/extension-installer": true
    }
  }
}
```

The installer is a transitive dependency of this package; `allow-plugins` must be set in the **consumer** project.

## Usage

### PHPStan

After installation, `phpstan/extension-installer` registers this package and all bundled extensions automatically. Your project root `phpstan.neon` should only define **paths**, optional **baseline**, and project-specific overrides:

- Do **not** manually `include` `vendor/demirkartal/eaglephp-code-quality/phpstan.neon` — the extension installer loads the base ruleset.
- Do **not** set `customRulesetUsed: true` in the consumer — it is declared in this package's `phpstan.neon`.
- With `customRulesetUsed: true`, built-in level rules are loaded via `config.level10.neon` in this package; setting only `parameters.level` in a consumer config does not register those rules.

```neon
# phpstan.neon (consumer project)
includes:
  - phpstan-baseline.neon

parameters:
  paths:
    - src
    - tests
```

```bash
./vendor/bin/phpstan analyse
```

### PHPStan next (PHP 8.5 target)

Optional forward-compat check against `phpVersion: 80500`. Include your project `phpstan.neon` (paths/baseline) plus the vendor next profile — `phpstan-next.neon` only overrides `phpVersion`, so the base ruleset is not loaded twice:

```neon
# phpstan.next.neon (consumer project)
includes:
  - phpstan.neon
  - vendor/demirkartal/eaglephp-code-quality/phpstan-next.neon
```

```bash
./vendor/bin/phpstan analyse -c phpstan.next.neon
```

### Laravel Pint

Pint is **not** auto-wired; pass the shared config explicitly:

```bash
./vendor/bin/pint --config vendor/demirkartal/eaglephp-code-quality/pint.json
```

Example `composer.json` scripts:

```json
{
  "scripts": {
    "format": "pint --config vendor/demirkartal/eaglephp-code-quality/pint.json",
    "format:check": "pint --config vendor/demirkartal/eaglephp-code-quality/pint.json --test",
    "phpstan": "phpstan analyse --memory-limit=512M"
  }
}
```

## Features

- **PHPStan Level 10** — strict static analysis for PHP `^8.4 || ^8.5` (lowest target: `80400`).
- **100% type coverage** — native types enforced for constants, properties, parameters, and return types.
- **Type Perfect rules** — disallows `empty()` / `isset()` on objects and parameter type removal in child classes.
- **Auto-wired PHPStan extensions** — via `phpstan/extension-installer` on `composer install`.
- **Unified Pint profile** — `per` preset with strict imports, PHPDoc, and trailing commas.

## Core stack

| Tool | Constraint |
| ------ | ------------ |
| PHP | `^8.4 \|\| ^8.5` |
| PHPStan | `^2.2` |
| Laravel Pint | `^1.30` |
| extension-installer | `^1.4` |

Active config: [`phpstan.neon`](phpstan.neon) — `config.level10.neon`, `phpVersion: 80400`.

## PHPStan rule levels

Levels are **cumulative** (level 6 includes 0–6).  
Source: [PHPStan Rule Levels](https://phpstan.org/user-guide/rule-levels) (PHPStan 2.x → **0–10**).

| Level | Focus | Checks |
| ------- | ------- | -------- |
| **0** | Basics | Unknown classes/functions/methods on `$this`, wrong argument counts, always-undefined variables |
| **1** | Variables & magic | Possibly undefined variables; unknown access on classes with `__call` / `__get` |
| **2** | Expressions & PHPDoc | Unknown methods on any expression; PHPDoc validation |
| **3** | Return & property types | Return types; types assigned to properties |
| **4** | Dead code | Always-false `instanceof` / type checks, dead `else`, unreachable code after `return` |
| **5** | Argument types | Argument type compatibility for calls |
| **6** | Missing typehints | Missing parameter, return, and property type declarations |
| **7** | Union members | Calling methods/properties that exist on only some union members |
| **8** | Null safety | Method/property access on nullable (`T\|null`) values without a null check |
| **9** | Explicit `mixed` | Almost no operations on explicitly typed `mixed` (pass-through only) |
| **10** | Implicit `mixed` | Untyped values treated as strictly as explicit `mixed` (PHPStan 2.0+) |

Level **10** does not replace level 9: it also treats *missing* types as `mixed` and forbids the same unsafe operations.

## Extensions & rule packages

`phpstan/extension-installer` automatically loads and activates:

| Package | Role |
| --------- | ------ |
| `bleedingEdge` | Upcoming PHPStan rule tightening and cutting-edge checks |
| `phpstan-strict-rules` | Strict `===`, type casting, bans weak PHP practices |
| `phpstan-phpunit` | Test case inference, mock returns, assertion narrowing |
| `phpstan-deprecation-rules` | Deprecated classes, methods, and functions |
| `type-coverage` | 100% native type coverage (`tomasvotruba/type-coverage`) |
| `type-perfect` | Narrowing rules: no `empty()`/`isset()` on objects, no param type removal |

## Strict analysis parameters

These flags extend beyond Level 10:

| Parameter | Purpose |
| ----------- | --------- |
| `customRulesetUsed` | `true` — required when the base ruleset is loaded via extension-installer (PHPStan 2.x); paired with `config.level10.neon` to register built-in level rules |
| **Type coverage (`100%`)** | `constant`, `declare`, `param_type`, `property_type`, `return_type` |
| **Type Perfect** | `no_empty_on_object`, `no_isset_on_object`, `no_param_type_removal` |
| `checkArgumentsPassedByReference` | Types of variables passed by reference |
| `checkBenevolentUnionTypes` | No lenient union assumptions; explicit narrowing required |
| `checkClassCaseSensitivity` | Exact case for class names and namespaces |
| `checkDynamicProperties` | Blocks runtime dynamic properties on unannotated objects |
| `checkMissingCallableSignature` | Rejects bare `callable` / `Closure`; requires full signature |
| `checkMissingOverrideMethodAttribute` | Requires `#[\Override]` on overridden methods |
| `checkTooWideParameterOutInProtectedAndPublicMethods` | `@param-out` broader than actual assignments |
| `checkTooWideReturnTypesInProtectedAndPublicMethods` | Return type broader than actual returns |
| `checkTooWideThrowTypesInProtectedAndPublicMethods` | `@throws` broader than thrown exceptions |
| `checkUninitializedProperties` | Typed properties must be initialized before access |
| `reportAlwaysTrueInLastCondition` | Unreachable branches in `match` / `if` chains |
| `reportAnyTypeWideningInVarTag` | No widening via `@var` beyond inferred type |
| `reportIgnoresWithoutComments` | Ignored errors require a descriptive comment |
| `reportPossiblyNonexistentConstantArrayOffset` | Array access with possibly missing constant keys |
| `reportPossiblyNonexistentGeneralArrayOffset` | Dynamic array keys not statically proven |
| `reportUnmatchedIgnoredErrors` | Stale `ignoreErrors` baseline entries fail CI |
| `reportWrongPhpDocTypeInVarTag` | `@var` contradicting inferred types |
| `rememberPossiblyImpureFunctionValues` | `false` — no assuming identical non-pure call results |
| `treatPhpDocTypesAsCertain` | `false` — defensive checks even when PHPDoc is present |
| `exceptions.reportUncheckedExceptionDeadCatch` | `catch` for exceptions never thrown in `try` |
| `exceptions.uncheckedExceptionClasses` | `LogicException`, `RuntimeException` exempt from checked flow |
| `exceptions.check.throwTypeCovariance` | Override may only throw same or narrower exceptions |
| `exceptions.check.tooWideImplicitThrowType` | Broad implicit throws without documentation |

`phpstan-strict-rules` also enables (via `rules.neon`): `checkExplicitMixedMissingReturn`, `reportMaybesInMethodSignatures`, `reportMaybesInPropertyPhpDocTypes`, `polluteScopeWithLoopInitialAssignments: false`, and related scope-pollution guards.

PHPStan does **not** require PHPDoc when native types suffice; missing type info is enforced at level 6+ and via type coverage.

## License

MIT — see [LICENSE](LICENSE).
