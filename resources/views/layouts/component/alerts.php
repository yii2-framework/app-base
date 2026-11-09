<?php

declare(strict_types=1);

use UIAwesome\Html\Core\Component\Alert;
use UIAwesome\Html\List\Ul;
use yii\web\View;

/** @var View $this View component instance. */
$session = Yii::$app->session;

$alerts = [];

if ($session->getIsActive() || $session->getHasSessionId()) {
    foreach (['success', 'error'] as $level) {
        if ($session->hasFlash($level)) {
            $alerts[] = Alert::tag()
                ->id(null)
                ->class("flash flash-{$level}")
                ->content((string) $session->getFlash($level))
                ->render();
        }
    }

    $errorBag = $session->getFlash('errors');

    if (is_array($errorBag) && $errorBag !== []) {
        $items = [];

        foreach ($errorBag as $messages) {
            foreach ((array) $messages as $message) {
                $items[] = (string) $message;
            }
        }

        $alerts[] = Ul::tag()->role('alert')->class('flash flash-errors')->items(...$items)->render();
    }
}
?>
<?= implode(PHP_EOL, $alerts);
