<?php

declare(strict_types=1);

use UIAwesome\Html\Flow\P;
use UIAwesome\Html\Heading\H1;
use yii\web\{HttpException, View};

/**
 * @var string $message User-facing error message (already filtered by {@see \yii\web\ErrorAction}).
 * @var string $name Exception name derived by {@see \yii\web\ErrorAction}.
 * @var \Throwable $exception Captured exception (synthesized as `NotFoundHttpException` when none was attached).
 * @var View $this View component instance.
 */
$code = $exception instanceof HttpException ? $exception->statusCode : 500;

$this->title = "Error {$code}";
?>
<?= H1::tag()->content($this->title)->render() ?>
<?= P::tag()->content($message)->render() ?>
