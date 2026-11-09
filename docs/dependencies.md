# Dependency policy

Dependencies are divided by whether generated applications need them at runtime or only this provider repository needs
them for development.

## Runtime dependencies

| Package group                                  | Purpose                                                                                                                                                 |
| ---------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `ext-ctype`                                    | Character classification used when validating token timestamps.                                                                                         |
| `yiisoft/yii2`                                 | Web and console application framework, standalone actions, Active Record, RBAC, validation, and dependency injection.                                   |
| `yiisoft/yii2-symfonymailer`                   | Mailer implementation used by account and contact workflows.                                                                                            |
| `yii2-extensions/debug`                        | Debug module referenced by `config/web.php` when `YII_ENV=dev`. It remains a runtime dependency so consumer installations receive the configured class. |
| `ui-awesome/html`                              | Typed semantic HTML elements used by layouts and page templates.                                                                                        |
| `ui-awesome/html-core-component`               | Menu, navigation, and alert components used by layout partials.                                                                                         |
| `ui-awesome/html-field`                        | Model-bound field rendering used by forms.                                                                                                              |
| `ui-awesome/html-interop`                      | HTML block-tag values referenced directly by model-bound field configuration.                                                                           |
| `ui-awesome/form-model` and `ui-awesome/model` | Contracts and metadata mapping used by `YiiFormModel`.                                                                                                  |

Direct requirements are retained when the repository references their public symbols. This avoids relying on a
transitive dependency that could disappear from another package's implementation details.

## Development dependencies

| Package group                                                                  | Purpose                                                                                                                           |
| ------------------------------------------------------------------------------ | --------------------------------------------------------------------------------------------------------------------------------- |
| Codeception core, modules, and Verify                                          | Unit and acceptance tests, Yii integration, fixtures, filesystem assertions, and fluent assertions.                               |
| `maglnet/composer-require-checker`                                             | Detects missing direct runtime requirements.                                                                                      |
| `php-forge/baseline` and `php-forge/coding-standard`                           | Shared repository files, ECS rules, and Rector configuration.                                                                     |
| PHPStan core, extension installer, strict rules, and `yii2-extensions/phpstan` | Maximum-level static analysis with Yii-aware types.                                                                               |
| `yii2-extensions/scaffold`                                                     | Develops and validates this provider and exposes the local scaffold CLI. Consumer projects must also require the plugin directly. |

`codeception/lib-innerbrowser` is intentionally not declared directly. The repository does not reference its API, and
`codeception/module-yii2` already supplies it transitively for the test environment.

## Compatibility constraints

- PHP `>=8.3` is the supported floor and must remain compatible with the CI matrix.
- Yii currently uses the `^22.0@dev` development line.
- The scaffold plugin uses the stable `^0.2` manifest and file-mode contract.
- `maglnet/composer-require-checker` is intentionally constrained to `~4.17.0`. This is the newest reviewed line that
  preserves the PHP 8.3 floor without adding an `ext-sodium` platform requirement. Reassess the pin when the platform
  matrix changes.

Constraints should not be widened or upgraded solely to silence `composer outdated`. Verify minimum PHP support,
required extensions, and the full test matrix first.

## Audit commands

Run the following after changing dependencies and during regular maintenance:

```bash
composer validate --strict
composer check-platform-reqs
composer audit
composer outdated --direct
composer licenses
composer check-dependencies
```

Interpret the results separately:

- `audit` reports known advisories and abandoned packages for the resolved dependency graph.
- `outdated --direct` identifies available releases but does not establish compatibility.
- `licenses` exposes the full transitive license set for legal review.
- `check-dependencies` finds symbols used without a corresponding direct runtime declaration. Its configuration also
  scans production configuration, entry points, RBAC files, mail templates, page templates, and the console entry point
  because those files are outside the root PSR-4 source tree. Provider-only test configuration remains covered by
  PHPStan and the test suite.

## Lock-file policy

This provider repository does not commit `composer.lock`, which allows CI to test the supported constraint range.
Consumer applications should commit their own `composer.lock` because they deploy a concrete dependency graph.

## License review

The currently resolved graph contains `enshrined/svg-sanitize` under `GPL-2.0-or-later` through UI Awesome's SVG support
and `ezyang/htmlpurifier` under `LGPL-2.1-or-later` through Yii. All other currently resolved packages report MIT or BSD
3-Clause metadata. Consumers must run `composer licenses` against their own lock file and perform the legal review
appropriate to how the application is distributed; Composer metadata is not legal advice.

## Next steps

- [Installation guide](installation.md)
- [Configuration reference](configuration.md)
- [Scaffold workflow](scaffold.md)
- [Presentation customization](frontend-overlays.md)
- [Testing and quality](testing.md)
