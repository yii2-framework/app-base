<?php

declare(strict_types=1);

namespace app\usecases\site;

use app\usecases\shared\view\ViewRendererInterface;
use yii\web\{Action, Response};

/**
 * Renders the static About page through the configured {@see ViewRendererInterface}.
 */
final class AboutAction extends Action
{
    public function __construct(private readonly ViewRendererInterface $view)
    {
        parent::__construct();
    }

    public function run(): Response|string
    {
        return $this->view->render(
            'site/about',
        );
    }
}
