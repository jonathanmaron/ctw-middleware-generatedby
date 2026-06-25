# PHP 8.5.7 Upgrade — `ctw/ctw-middleware-generatedby`

- **Branch:** `php85` (cut from `master`)
- **Runtime:** PHP 8.3.31 → **8.5.7**
- **Date:** 2026-06-25

This is a **TODO list** of the changes required for this package to run cleanly
under PHP 8.5.7. Boxes are intentionally left unchecked.

---

## ✅ Applied on `php85` (diactoros blocker resolved)

> Supersedes the "❌ FAILS" analysis below.

`composer.json` changes:

- [x] `laminas/laminas-diactoros` `^2.11` → **`^3.0`** (installs 3.8.0).
- [x] `ctw/ctw-middleware` `^4.0` → **`dev-php85`** (diactoros 3 / middlewares-utils 4 / servicemanager 4.5).
- [x] `ramsey/uuid ^4.1` left as-is — resolves a PHP 8.5-compatible 4.x release.

**Result:** `composer update -W` is green; `phpunit --no-coverage` reports
**41 tests, 57 assertions, 0 deprecations** (the `middlewares/utils` deprecations
are cleared by v4). One residual PHPUnit "mock without expectations" notice
(PHPUnit 12.5; not a PHP 8.5 issue) plus the shared PHPStan unmatched-ignore (§3).
Re-tag `ctw/ctw-middleware` to stable before merge.

> ⚠️ **This package declares `laminas/laminas-diactoros` directly**, so it needs
> the Diactoros bump in its *own* `composer.json` (§1), not just via
> `ctw/ctw-middleware`.

Detection commands used:

```bash
composer update -W
php vendor/bin/phpunit --no-coverage --display-deprecations --display-warnings --display-notices --display-errors
composer rector      # rector --dry-run
composer phpstan
```

---

## 1. `composer update -W` — ❌ FAILS (hard blocker, direct + transitive)

```
Problem 1
  - Root composer.json requires laminas/laminas-diactoros ^2.11
  - laminas/laminas-diactoros[2.11 ... 2.26] require php ~8.0 || ~8.1 || ~8.2 || ~8.3
    -> your php version (8.5.7) does not satisfy that requirement.
```

The `laminas/laminas-diactoros` 2.x line caps PHP at `~8.3.0`. This package
requires it **directly** (`^2.11`) *and* transitively via `ctw/ctw-middleware
^4.0`.

- [ ] **`composer.json`** — bump `laminas/laminas-diactoros` from `^2.11` to
  **`^3.0`** (Diactoros 3.x supports PHP 8.4/8.5).
- [ ] **Blocked on `ctw/ctw-middleware`** too — bump its constraint to the new,
  Diactoros-3-based release once it is published (see
  `ctw-middleware/dev-php85/UPDATE.md` §1).
- [ ] Check `ramsey/uuid ^4.1` — confirm `composer update -W` selects a PHP
  8.5-compatible 4.x release (it should; flagged for completeness).
- [ ] Re-run `composer update -W` and resolve any secondary conflicts that
  surface only after Diactoros is unblocked (e.g. `psr/http-message` may need
  widening to `^1.1 || ^2.0` for Diactoros 3).

> §2 was captured against the existing (master) lockfile because the update
> aborts.

---

## 2. PHP 8.5 runtime deprecations

All originate in the **third-party** `middlewares/utils` dependency — the
"implicitly nullable parameter" deprecation. **No first-party `src/` change is
required.**

| Location | Method / parameter |
| --- | --- |
| `vendor/middlewares/utils/src/Factory.php:88` | `Factory::createUploadedFile()` `$size` |
| `vendor/middlewares/utils/src/Factory.php:90` | `Factory::createUploadedFile()` `$filename` |
| `vendor/middlewares/utils/src/Factory.php:91` | `Factory::createUploadedFile()` `$mediaType` |
| `vendor/middlewares/utils/src/Dispatcher.php:21` | `Dispatcher::run()` `$request` |
| `vendor/middlewares/utils/src/CallableHandler.php:25` | `CallableHandler::__construct()` `$responseFactory` |

- [ ] Resolved by updating `middlewares/utils` once §1 is cleared; escalate
  upstream if the latest release still emits them.

---

## 3. QA tooling issues

- [ ] **PHPStan unmatched ignore pattern** (`missingType.generics`) — fix
  centrally in **`ctw/ctw-qa`** (`ctw-qa/dev-php85/UPDATE.md` §3). PHPStan
  currently reports **1 error**, this spurious one only.

---

## 4. Notes (non-blocking)

- Run locally with `--no-coverage` (no Xdebug/PCOV here). Not a PHP 8.5 issue.

---

## 5. Verification snapshot (current state on `php85`)

| Check | Result |
| --- | --- |
| `composer update -W` | ❌ fails — direct + transitive `laminas-diactoros` 2.x (§1) |
| PHPUnit (`--no-coverage`, stale deps) | 41 tests, 57 assertions, **5 deprecations** (`middlewares/utils`, §2) |
| Rector (dry-run) | ✅ no changes proposed |
| PHPStan | ❌ 1 error (shared unmatched-ignore, §3) |

**First-party work needed here:** the direct `laminas-diactoros` bump in §1.
Runtime deprecations (§2) and §3 are gated on upstream fixes.
