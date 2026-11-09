# Scaffold workflow

The [`php-forge/scaffold`][scaffold] Composer plugin applies this provider to a consumer project and records the result
in `scaffold-lock.json`.

## Provider manifest

The root `scaffold.json` declares the complete public file set:

```json
{
    "copy": [
        "src",
        "config",
        "rbac",
        "resources",
        "public",
        "yii",
        "runtime/.gitignore"
    ],
    "exclude": [
        "config/test.php",
        "config/test_db.php",
        "public/index-test.php"
    ],
    "modes": {
        "config/*.php": "preserve",
        "rbac/*.php": "preserve",
        "public/assets/.gitignore": "preserve",
        "public/images/**": "preserve",
        "runtime/.gitignore": "preserve"
    }
}
```

Only declared, non-excluded paths are copied. Provider test configuration and its test web entry point remain in this
repository alongside metadata, development configuration, documentation, and tests.

## File modes

This provider uses two modes:

| Mode       | Behavior during Composer scaffold application                                                                                                      |
| ---------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| `replace`  | Writes a missing file and updates a tracked file when its current hash still matches the lock. A consumer-modified file is skipped with a warning. |
| `preserve` | Writes only when the destination does not exist. An existing file is never overwritten by automatic application.                                   |

The preserved paths contain configuration, RBAC state, public images, and writable-directory placeholders. Other
application code and templates default to `replace`.

The plugin also supports `append` and `prepend`; this provider does not use them. See the upstream [file-mode
reference][modes] for their contracts.

## Lock file

`scaffold-lock.json` records each destination, provider, source path, mode, and content hash. Consumer applications
should commit it next to `composer.lock`.

The two lock files serve different purposes:

- `composer.lock` fixes dependency versions.
- `scaffold-lock.json` fixes copied-file ownership and detects local drift.

## Inspection commands

```bash
vendor/bin/scaffold providers
vendor/bin/scaffold status
vendor/bin/scaffold diff src/usecases/user/LoginAction.php
```

`status` reports each tracked path as `synced`, `modified`, or `missing`. `diff` compares a destination with its current
provider source.

## Reapply or take ownership

Force a tracked file back to the provider version:

```bash
vendor/bin/scaffold reapply src/usecases/user/LoginAction.php --force
```

Without `--force`, reapply skips consumer-modified `replace` files. A `preserve` file also requires `--force` before an
existing destination can be overwritten.

Stop tracking a file without deleting it:

```bash
vendor/bin/scaffold eject resources/views/site/home.php --yes
```

After ejection, the application owns the file and future provider operations do not update it.

## Provider updates

Use this sequence after changing the provider constraint:

```bash
composer update yii2-framework/app-base php-forge/scaffold --with-dependencies
vendor/bin/scaffold status
```

Review every warning and diff before forcing a file. Do not overwrite application modifications merely to make status
output clean.

## Uninstalling

Remove the provider from `require` and `extra.scaffold.allowed-packages`, then run `composer update`. Review the
remaining application files manually; removing the package does not imply that consumer-owned code should be deleted.

## Next steps

- [Installation guide](installation.md)
- [Configuration reference](configuration.md)
- [Presentation customization](frontend-overlays.md)
- [Dependency policy](dependencies.md)
- [Testing and quality](testing.md)

[modes]: https://github.com/php-forge/scaffold/blob/main/docs/modes.md
[scaffold]: https://github.com/php-forge/scaffold
