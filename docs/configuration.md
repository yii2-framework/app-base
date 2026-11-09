# Configuration reference

All configuration and RBAC files copied to consumers use scaffold `preserve` mode. They are created when missing and are
not overwritten by later provider updates.

## Database configuration

`config/db.php` defaults to SQLite:

```php
return [
    'class' => \yii\db\Connection::class,
    'dsn' => 'sqlite:' . dirname(__DIR__) . '/runtime/db.sqlite',
];
```

Configure the `dsn`, credentials, and driver options required by the target database. Both the web and console
applications use this file, so migrations and web requests share the same connection.

The provider-only `config/test_db.php` uses a separate SQLite database at `tests/support/data/test.sqlite`.

## Application parameters

`config/params.php` is loaded by the web and console applications.

| Key                                    | Default                  | Purpose                                                                         |
| -------------------------------------- | ------------------------ | ------------------------------------------------------------------------------- |
| `admin.email`                          | `admin@example.com`      | Email used by the administrator seed migration.                                 |
| `admin.password`                       | `admin`                  | Password used by the administrator seed migration. Replace it before migration. |
| `admin.username`                       | `admin`                  | Username used by the administrator seed migration.                              |
| `adminEmail`                           | `admin@example.com`      | Recipient for contact messages.                                                 |
| `cookieValidationKey`                  | Generated during install | Secret used to sign request cookies. `config/web.php` rejects an empty value.   |
| `senderEmail`                          | `noreply@example.com`    | Sender address for application mail.                                            |
| `senderName`                           | `Example.com mailer`     | Sender display name for application mail.                                       |
| `supportEmail`                         | `support@example.com`    | Support address used by account workflows.                                      |
| `user.emailVerificationTokenExpire`    | `86400`                  | Verification-token lifetime in seconds.                                         |
| `user.passwordMinLength`               | `8`                      | Minimum password length for signup and password reset.                          |
| `user.passwordResetTokenExpire`        | `3600`                   | Password-reset-token lifetime in seconds.                                       |
| `user.resendVerificationEmailCooldown` | `60`                     | Delay between verification-email resend requests, in seconds.                   |

## Web application

`config/web.php` configures:

- `actionNamespace` as `app\usecases`, allowing routes such as `site/home` to resolve standalone action classes.
- `user.identityClass` as `app\usecases\shared\user\User`.
- `yii\rbac\PhpManager` using the preserved files under `rbac/`.
- `site/error` as the standalone error action.
- pretty URLs without the entry-script name.
- CSRF validation and a required cookie validation key.
- `ViewRendererInterface` with the bundled `PhpViewRenderer` implementation.
- `MailerInterface` as a singleton Symfony Mailer adapter using templates under `resources/mail`.

The default mailer uses file transport and writes messages below `runtime/`. Replace that development setting with a
real transport before production.

When `YII_ENV=dev`, the debug module is bootstrapped. The debug package is a runtime dependency because the shipped
configuration references its module class.

## Console application

`config/console.php` provides:

- `app\commands` as the controller namespace.
- `app\migrations` as the migration namespace.
- the same database, cache, RBAC, and parameter configuration used by the application.
- `./yii serve` with `public/` as its document root.

## Test application

`config/test.php` is isolated from the normal application configuration. It uses the test database, disables CSRF for
test requests, captures mail through file transport, and binds the same PHP view renderer used by the application.

`public/index-test.php` is fail-closed: it returns `404` unless the process environment contains `YII_ENV=test`, and it
then accepts only loopback clients. These test files and `config/test_db.php` are excluded from consumer scaffolds; they
exist only for this package's test suite.

## RBAC

Yii `PhpManager` reads these preserved files:

- `rbac/items.php` for roles and permissions.
- `rbac/rules.php` for serialized rule definitions.
- `rbac/assignments.php` for user assignments.

Applications may edit these files or replace the `authManager` component with another Yii RBAC backend.

## Mail and views

Mail templates under `resources/mail/` and page templates under `resources/views/` use `replace` mode. An unmodified
template receives provider updates; a consumer-modified template is skipped. Use `scaffold eject` when the application
should own a template permanently.

## Production checklist

- [ ] Set `YII_ENV=prod`; `public/index.php` otherwise defaults to the development environment.
- [ ] Use a generated, private `cookieValidationKey`.
- [ ] Replace the default administrator credentials before applying the seed migration.
- [ ] Configure a production database and restrict its credentials.
- [ ] Replace file mail transport with the required production transport.
- [ ] Set valid administrator, sender, and support email addresses.
- [ ] Add application-specific rate limits and abuse controls to public form endpoints.
- [ ] Review RBAC roles and assignments.
- [ ] Keep `runtime/` and `public/assets/` writable only by the required service account.
- [ ] Run migrations, tests, static analysis, and `composer audit` during deployment validation.

## Next steps

- [Installation guide](installation.md)
- [Scaffold workflow](scaffold.md)
- [Presentation customization](frontend-overlays.md)
- [Dependency policy](dependencies.md)
- [Testing and quality](testing.md)
