<?php

declare(strict_types=1);

namespace app\usecases\shared\view;

use Yii;

/**
 * Default {@see ViewRendererInterface} implementation that resolves PHP view templates under `@app/resources/views`
 * and wraps them in the shared `layouts/main.php` layout.
 *
 * Used by `app-base` out of the box. Applications that use another presentation strategy can bind a different
 * implementation in the DI container.
 */
final class PhpViewRenderer implements ViewRendererInterface
{
    public function render(string $view, array $params = []): string
    {
        $appView = Yii::$app->view;

        $content = $appView->renderFile(Yii::getAlias("@app/resources/views/{$view}.php"), $params);

        return $appView->renderFile(
            Yii::getAlias('@app/resources/views/layouts/main.php'),
            ['content' => $content],
        );
    }
}
