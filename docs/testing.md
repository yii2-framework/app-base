# Testing and quality

The repository validates behavior, types, coding standards, dependency declarations, and Composer metadata.

Install development dependencies before running these commands:

```bash
composer install
vendor/bin/codecept build
```

## Test suites

Run all Codeception suites:

```bash
composer tests
```

The repository contains:

```text
tests/
├── acceptance/              Request-pipeline access and verb checks
├── functional/              Integration suite reserved for application scenarios
├── unit/
│   ├── migrations/          Schema and administrator seed tests
│   ├── scaffold/            Provider manifest contract tests
│   └── usecases/            Actions, forms, user model, filters, and view adapters
└── support/
    ├── data/                SQLite database and fixtures
    ├── fixtures/            Yii fixture definitions
    ├── spies/               Test doubles requiring application inheritance
    └── MailerBootstrap.php  Shared mailer binding for test assertions
```

`tests/_bootstrap.php` prepares the test application and migrations. `config/test.php` uses the isolated SQLite database
in `tests/support/data/test.sqlite`, disables CSRF for test requests, and captures outgoing mail.

Forward a suite or test path after Composer's `--` separator:

```bash
composer tests -- unit
composer tests -- unit tests/unit/usecases/user/LoginFormTest.php
composer tests -- acceptance
```

## Coverage

Codeception includes `src/` in coverage collection. Generate text, XML, or HTML output only in an environment with a
supported coverage driver:

```bash
composer tests -- --coverage --coverage-text --coverage-xml --coverage-html
```

Generated reports are stored under `tests/support/output/` and are not documentation of a permanent coverage percentage.
Review the current CI report when making coverage decisions.

## Static analysis

Run PHPStan at the repository's configured maximum level and strict rules:

```bash
composer static
```

Configuration is in `phpstan.neon`; the Yii application bootstrap used by analysis is
`tests/support/phpstan-config.php`.

## Coding standards and automated refactoring

Apply the configured PER 3.0 and PSR-12 rules:

```bash
composer ecs
```

Apply Rector rules:

```bash
composer rector
```

Generated Codeception actors are excluded from both tools. To inspect Rector without writing files:

```bash
vendor/bin/rector process --dry-run
```

## Dependency and metadata checks

```bash
composer validate --strict
composer check-platform-reqs
composer check-dependencies
composer audit
composer outdated --direct
composer licenses
```

`composer check-dependencies` verifies that external symbols referenced by `src/` have corresponding runtime
requirements. See [dependencies.md](dependencies.md) for the dependency policy and compatibility pin rationale.

## Recommended local sequence

```bash
composer validate --strict
composer check-platform-reqs
composer check-dependencies
composer static
vendor/bin/rector process --dry-run
composer ecs
composer tests
git diff --check
```

Because `composer ecs` and `composer rector` write changes, review the working tree after running them.

## Next steps

- [Installation guide](installation.md)
- [Configuration reference](configuration.md)
- [Scaffold workflow](scaffold.md)
- [Presentation customization](frontend-overlays.md)
- [Dependency policy](dependencies.md)
