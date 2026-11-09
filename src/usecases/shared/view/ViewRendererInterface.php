<?php

declare(strict_types=1);

namespace app\usecases\shared\view;

use yii\web\Response;

/**
 * Renders a named view into a string body or an HTTP response.
 *
 * Decouples standalone actions in `app\usecases` from the application's presentation strategy. Applications can bind
 * another implementation in the DI container, so actions remain agnostic to how the view is produced.
 *
 * View names are route-qualified (`site/about`, `user/login`) so renderers can map them deterministically without
 * inspecting the calling action.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
interface ViewRendererInterface
{
    /**
     * Renders the named view with the supplied parameters.
     *
     * @param string $view Route-qualified view name (for example, `site/about`, `user/login`).
     * @param array<string, mixed> $params Data to expose to the view layer.
     *
     * @return Response|string Rendered body, or an `HTTP` response when the renderer needs to send headers directly.
     */
    public function render(string $view, array $params = []): Response|string;
}
