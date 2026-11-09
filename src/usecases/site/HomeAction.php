<?php

declare(strict_types=1);

namespace app\usecases\site;

use app\usecases\shared\view\ViewRendererInterface;
use yii\web\{Action, Response};

/**
 * Renders the application home page through the configured {@see ViewRendererInterface}.
 *
 * Route: `site/home` (also reached through the application `defaultRoute`).
 */
final class HomeAction extends Action
{
    /**
     * Creates a new instance with the configured renderer.
     *
     * @param ViewRendererInterface $view Renderer used to produce the home page.
     */
    public function __construct(private readonly ViewRendererInterface $view)
    {
        parent::__construct();
    }

    /**
     * Renders the home page.
     *
     * @return Response|string Rendered home view, or an `HTTP` response when the renderer sends headers directly.
     */
    public function run(): Response|string
    {
        return $this->view->render(
            'site/home',
        );
    }
}
