# Installation guide

This guide creates a new Yii2 application from `yii2-extensions/app-base`.

## Requirements

- PHP `8.3` or later with ctype support, the extensions required by Yii, and the selected database driver.
- Composer `2.10` or later.
- SQLite for the default database configuration, or another Yii-supported database configured before migration.

The package is a scaffold provider. The scaffold plugin copies application files into the project root; the copied files
are not hidden behind a framework installer or generator.

## 1. Create `composer.json`

Create an empty directory and add this root package manifest:

```json
{
    "name": "my-company/my-app",
    "type": "project",
    "description": "A Yii2 application based on yii2-extensions/app-base.",
    "license": "proprietary",
    "minimum-stability": "dev",
    "prefer-stable": true,
    "require": {
        "php": ">=8.3",
        "yii2-extensions/app-base": "^0.1@dev",
        "yii2-extensions/scaffold": "^0.2"
    },
    "autoload": {
        "psr-4": {
            "app\\": "src"
        }
    },
    "extra": {
        "scaffold": {
            "allowed-packages": [
                "yii2-extensions/app-base"
            ]
        },
        "yii\\composer\\Installer::postCreateProject": {
            "setPermission": [
                {
                    "runtime": "0775",
                    "public/assets": "0775",
                    "yii": "0755"
                }
            ]
        },
        "yii\\composer\\Installer::postInstall": {
            "generateCookieValidationKey": [
                "config/params.php"
            ]
        }
    },
    "config": {
        "allow-plugins": {
            "yii2-extensions/scaffold": true,
            "yiisoft/yii2-composer": true
        }
    },
    "scripts": {
        "post-create-project-cmd": [
            "yii\\composer\\Installer::postCreateProject",
            "yii\\composer\\Installer::postInstall"
        ],
        "post-install-cmd": [
            "yii\\composer\\Installer::postInstall"
        ]
    }
}
```

Important entries:

| Entry                                   | Reason                                                                                                                                     |
| --------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------ |
| `minimum-stability` and `prefer-stable` | The application and Yii constraints currently resolve development branches while preferring stable transitive packages.                    |
| `autoload.psr-4.app\\`                  | Loads the scaffolded classes from the root `src/` directory instead of the provider copy under `vendor/`.                                  |
| `yii2-extensions/scaffold`              | Installs the Composer plugin that applies and tracks provider files. It is a runtime requirement so `--no-dev` installs retain the plugin. |
| `extra.scaffold.allowed-packages`       | Explicitly authorizes this provider to write into the project root.                                                                        |
| `allow-plugins`                         | Explicitly authorizes the scaffold and Yii Composer plugins.                                                                               |
| Yii installer callbacks                 | Set initial permissions and generate a non-empty cookie validation key.                                                                    |

## 2. Install dependencies and apply the scaffold

```bash
composer install
```

The first install creates `src/`, `config/`, `rbac/`, `resources/`, `public/`, `runtime/`, `yii`, and
`scaffold-lock.json`.

Inspect the result:

```bash
vendor/bin/scaffold providers
vendor/bin/scaffold status
```

Commit `composer.lock` and `scaffold-lock.json` in the consumer application. They lock dependency resolution and
scaffold state independently.

## 3. Configure the application

Before running migrations:

1. Confirm that `cookieValidationKey` in `config/params.php` is non-empty.
2. Replace `admin.username`, `admin.email`, and `admin.password`.
3. Choose the database in `config/db.php`.
4. Configure mail delivery if the application must send real messages.

The configuration files use scaffold `preserve` mode, so subsequent installs do not overwrite these values.

## 4. Run migrations

```bash
./yii migrate
```

The migrations create the user table and seed the administrator configured in `config/params.php`. The default SQLite
database is `runtime/db.sqlite`.

## 5. Start a local server

```bash
php -S localhost:8080 -t public public/router.php
```

Alternatively:

```bash
./yii serve --router=public/router.php
```

The router sends application requests to `public/index.php` and lets the PHP server return existing static files.

## Next steps

- [Configuration reference](configuration.md)
- [Scaffold workflow](scaffold.md)
- [Presentation customization](frontend-overlays.md)
- [Dependency policy](dependencies.md)
- [Testing and quality](testing.md)
