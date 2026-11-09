# Presentation customization

`app-base` includes a complete PHP presentation layer:

- `PhpViewRenderer` resolves route-qualified templates below `resources/views/`.
- `resources/views/layouts/main.php` supplies the document layout and partials.
- site and user views generate semantic HTML through UI Awesome components.
- `YiiFormModel` adapts Yii model metadata and validation errors to the field components.

No CSS framework, JavaScript runtime, or asset build pipeline is imposed. A consumer can style the semantic markup, take
ownership of individual views, or replace the renderer.

## Customize the bundled PHP views

Edit a file below `resources/views/` for application-specific markup. These files use scaffold `replace` mode:

- unmodified files can receive provider updates;
- modified files are detected and skipped;
- ejected files are no longer tracked.

Review and take ownership of a view explicitly:

```bash
vendor/bin/scaffold diff resources/views/site/home.php
vendor/bin/scaffold eject resources/views/site/home.php --yes
```

Add styles and scripts through an application-owned asset bundle or build pipeline. The base package does not assume a
specific frontend framework.

## Replace the renderer

Standalone actions depend on `app\usecases\shared\view\ViewRendererInterface`, not directly on Yii PHP views. A consumer
can implement that interface for another response strategy and change the DI binding in `config/web.php`:

```php
use app\presentation\ApplicationViewRenderer;
use app\usecases\shared\view\ViewRendererInterface;

return [
    'container' => [
        'definitions' => [
            ViewRendererInterface::class => ApplicationViewRenderer::class,
        ],
    ],
];
```

The interface accepts route-qualified names such as `site/about` and `user/login`, an associative parameter array, and
returns either a string or `yii\web\Response`. Business workflows, redirects, validation, and access rules remain in the
standalone actions.

## Compose another scaffold provider

A separate provider can contribute a complete presentation layer. Declare it after `app-base` so it has precedence for
overlapping destinations:

```json
{
    "require": {
        "yii2-extensions/app-base": "^0.1@dev",
        "yii2-extensions/scaffold": "^0.2",
        "vendor/application-presentation": "^1.0"
    },
    "extra": {
        "scaffold": {
            "allowed-packages": [
                "yii2-extensions/app-base",
                "vendor/application-presentation"
            ]
        }
    }
}
```

An alternate provider may replace views, add assets, and bind a renderer. It should avoid duplicating authentication,
mail, persistence, and routing logic already owned by `src/usecases/`.

Consult the [scaffold provider documentation][providers] before publishing an additional provider.

## Next steps

- [Installation guide](installation.md)
- [Configuration reference](configuration.md)
- [Scaffold workflow](scaffold.md)
- [Dependency policy](dependencies.md)
- [Testing and quality](testing.md)

[providers]: https://github.com/yii2-extensions/scaffold/blob/main/docs/providers.md
